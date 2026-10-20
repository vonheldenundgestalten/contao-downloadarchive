<?php

declare(strict_types=1);

/*
 * This file is part of the felixpfeiffer/contao-downloadarchive package.
 *
 * (c) Felix Pfeiffer
 *
 * @license LGPL-3.0-or-later
 */

namespace FelixPfeiffer\DownloadarchiveBundle\Downloadarchive;

use Contao\Config;
use Contao\ContentModel;
use Contao\CoreBundle\Exception\PageNotFoundException;
use Contao\CoreBundle\Exception\PageOutOfRangeException;
use Contao\CoreBundle\Exception\ResponseException;
use Contao\CoreBundle\File\Metadata;
use Contao\CoreBundle\Filesystem\FileDownloadHelper;
use Contao\CoreBundle\Filesystem\FilesystemItem;
use Contao\CoreBundle\Filesystem\PublicUri\ContentDispositionOption;
use Contao\CoreBundle\Filesystem\VirtualFilesystemInterface;
use Contao\CoreBundle\Framework\ContaoFramework;
use Contao\CoreBundle\Image\Studio\Figure;
use Contao\CoreBundle\Image\Studio\Studio;
use Contao\CoreBundle\Pagination\PaginationConfig;
use Contao\CoreBundle\Pagination\PaginationFactoryInterface;
use Contao\CoreBundle\Security\ContaoCorePermissions;
use Contao\CoreBundle\Twig\FragmentTemplate;
use Contao\Date;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use FelixPfeiffer\DownloadarchiveBundle\Model\DownloadarchiveItemModel;
use FelixPfeiffer\DownloadarchiveBundle\Model\DownloadarchiveModel;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\Uid\Uuid;

/**
 * Builds the download list of the content element and the front end module,
 * which share the same configuration fields (downloadarchive, downloadSorting,
 * downloadNumberOfItems, perPage, downloadShowMeta, downloadHideDate).
 *
 * Files are served through signed URLs ({@see FileDownloadHelper}) unless they
 * are in a public folder, so protected items cannot be downloaded by guessing
 * the path.
 *
 * @phpstan-type Entry array{archive: DownloadarchiveModel, item: DownloadarchiveItemModel, file: FilesystemItem}
 */
class DownloadarchiveRenderer
{
    public const SORTING_OPTIONS = ['sorting ASC', 'sorting DESC', 'tstamp ASC', 'tstamp DESC', 'title ASC', 'title DESC'];

    public function __construct(
        private readonly ContaoFramework $framework,
        private readonly VirtualFilesystemInterface $filesStorage,
        private readonly FileDownloadHelper $fileDownloadHelper,
        private readonly Studio $studio,
        private readonly Security $security,
        private readonly PaginationFactoryInterface $paginationFactory,
    ) {
    }

    public function render(FragmentTemplate $template, ContentModel|ModuleModel $model, Request $request, PageModel|null $page): void
    {
        // Unique per element, so downloads and pagination of several archives on
        // one page do not interfere
        $context = ($model instanceof ContentModel ? 'ce' : 'mod').$model->id;

        $entries = $this->getEntries(StringUtil::deserialize($model->downloadarchive, true), (string) $model->downloadSorting);

        if ($model->downloadNumberOfItems > 0) {
            $entries = \array_slice($entries, 0, (int) $model->downloadNumberOfItems);
        }

        $this->handleDownload($request, $entries, $context);

        $pagination = null;

        if ($model->perPage > 0 && [] !== $entries) {
            try {
                $pagination = $this->paginationFactory->create(new PaginationConfig('page_'.$context, \count($entries), (int) $model->perPage));
            } catch (PageOutOfRangeException $e) {
                throw new PageNotFoundException(\sprintf('Page not found: %s', $request->getUri()), previous: $e);
            }

            $entries = $pagination->getItemsForPage($entries);
        }

        $this->framework->initialize();

        $dateFormat = $page?->dateFormat ?: (string) $this->framework->getAdapter(Config::class)->get('dateFormat');

        $template->set('downloads', array_map(
            fn (array $entry): array => $this->compileEntry($entry, $request, $context, $dateFormat),
            $entries,
        ));

        $template->set('pagination', $pagination);
        $template->set('show_meta', (bool) $model->downloadShowMeta);
        $template->set('hide_date', (bool) $model->downloadHideDate);
    }

    /**
     * Returns the downloadable items of the published archives the current
     * visitor may see, in the requested order.
     *
     * @param array<int|string> $archiveIds
     *
     * @return list<Entry>
     */
    public function getEntries(array $archiveIds, string $sorting = ''): array
    {
        $this->framework->initialize();

        $archives = $this->framework->getAdapter(DownloadarchiveModel::class)->findPublishedByIds($archiveIds);

        if (null === $archives) {
            return [];
        }

        $allowedExtensions = array_map(
            strtolower(...),
            StringUtil::trimsplit(',', (string) $this->framework->getAdapter(Config::class)->get('allowedDownload')),
        );

        $itemAdapter = $this->framework->getAdapter(DownloadarchiveItemModel::class);
        $entries = [];

        foreach ($archives as $archive) {
            foreach ($itemAdapter->findPublishedByPid((int) $archive->id) ?? [] as $item) {
                if (!$this->isVisible($item) || !$file = $this->getFile($item)) {
                    continue;
                }

                if (!\in_array($file->getExtension(true), $allowedExtensions, true)) {
                    continue;
                }

                $entries[] = ['archive' => $archive, 'item' => $item, 'file' => $file];
            }
        }

        return $this->sort($entries, $sorting);
    }

    /**
     * Streams the requested file if the signed download URL belongs to this
     * element and the file is still part of its list.
     *
     * @param list<Entry> $entries
     */
    private function handleDownload(Request $request, array $entries, string $context): void
    {
        $response = $this->fileDownloadHelper->handle(
            $request,
            $this->filesStorage,
            static function (FilesystemItem $file, array $downloadContext) use ($entries, $context): Response|null {
                // Leave downloads of other elements on the same page to them
                if ($context !== ($downloadContext['id'] ?? null)) {
                    return new Response('', Response::HTTP_NO_CONTENT);
                }

                foreach ($entries as $entry) {
                    if ($entry['file']->getPath() === $file->getPath()) {
                        return null;
                    }
                }

                return new Response('The resource can not be accessed anymore.', Response::HTTP_GONE);
            },
        );

        if ($response instanceof StreamedResponse || $response instanceof BinaryFileResponse) {
            throw new ResponseException($response);
        }
    }

    /**
     * @param Entry $entry
     */
    private function compileEntry(array $entry, Request $request, string $context, string $dateFormat): array
    {
        ['archive' => $archive, 'item' => $item, 'file' => $file] = $entry;

        $href = $this->generateDownloadUrl($file, $request, $context);
        $lastModified = $file->getLastModified();

        return [
            'href' => $href,
            'file' => $file,
            'text' => $item->title,
            'description' => (string) $item->description,
            'archive' => $archive->title,
            'css_class' => $archive->class,
            'date' => $lastModified ? $this->framework->getAdapter(Date::class)->parse($dateFormat, $lastModified) : '',
            'size' => $file->getFileSize(),
            'figure' => $this->buildFigure($item, $href, $context),
            'item' => $item->row(),
        ];
    }

    private function generateDownloadUrl(FilesystemItem $file, Request $request, string $context): string
    {
        if ($publicUri = $this->filesStorage->generatePublicUri($file->getPath(), new ContentDispositionOption(false))) {
            return (string) $publicUri;
        }

        return $this->fileDownloadHelper->generateDownloadUrl($request->getUri(), $file->getPath(), $file->getName(), ['id' => $context]);
    }

    private function buildFigure(DownloadarchiveItemModel $item, string $href, string $context): Figure|null
    {
        if (!$item->addImage || !$item->imgSRC) {
            return null;
        }

        $figureBuilder = $this->studio
            ->createFigureBuilder()
            ->fromUuid($item->imgSRC)
            ->setSize($item->size)
        ;

        $metadata = array_filter([
            Metadata::VALUE_ALT => (string) $item->alt,
            Metadata::VALUE_CAPTION => (string) $item->caption,
        ]);

        if ([] !== $metadata) {
            $figureBuilder->setOverwriteMetadata(new Metadata($metadata));
        }

        match ((string) $item->useImage) {
            DownloadarchiveItemModel::IMAGE_LINK_LIGHTBOX => $figureBuilder
                ->enableLightbox()
                ->setLightboxGroupIdentifier('downloadarchive_'.$context),
            DownloadarchiveItemModel::IMAGE_LINK_DOWNLOAD => $figureBuilder->setLinkHref($href),
            default => null,
        };

        return $figureBuilder->buildIfResourceExists();
    }

    private function isVisible(DownloadarchiveItemModel $item): bool
    {
        if (!$item->protected) {
            return true;
        }

        // Group -1 stands for "guests" (not logged in)
        return $this->security->isGranted(ContaoCorePermissions::MEMBER_IN_GROUPS, StringUtil::deserialize($item->groups, true));
    }

    private function getFile(DownloadarchiveItemModel $item): FilesystemItem|null
    {
        if (!$item->singleSRC) {
            return null;
        }

        try {
            $file = $this->filesStorage->get(Uuid::fromBinary($item->singleSRC));
        } catch (\Throwable) {
            return null;
        }

        return $file?->isFile() ? $file : null;
    }

    /**
     * Sorts across all archives, e.g. "title ASC"; without a sorting the order
     * of the selected archives and the items within them is kept.
     *
     * @param list<Entry> $entries
     *
     * @return list<Entry>
     */
    private function sort(array $entries, string $sorting): array
    {
        if (!\in_array($sorting, self::SORTING_OPTIONS, true)) {
            return $entries;
        }

        [$field, $direction] = explode(' ', $sorting);

        usort(
            $entries,
            static function (array $a, array $b) use ($field): int {
                $valueA = $a['item']->{$field};
                $valueB = $b['item']->{$field};

                return 'title' === $field
                    ? strnatcasecmp((string) $valueA, (string) $valueB)
                    : (int) $valueA <=> (int) $valueB;
            },
        );

        return 'DESC' === $direction ? array_reverse($entries) : $entries;
    }
}
