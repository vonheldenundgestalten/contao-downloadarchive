# Contao Download Archive

Organize files from the file manager in download archives. Each file gets its
own title, description, optional preview image and visibility (member groups,
guests, publish dates), independent of the file meta data.

The files are listed by the **Download archive** content element or front end
module, which can combine several archives, sort them together, limit and
paginate the list.

Requires Contao 5.7 and PHP 8.3.

Maintained by von Helden und Gestalten. This is a fork of
[felixpfeiffer/contao-downloadarchive](https://github.com/felixpfeiffer/contao-downloadarchive)
by Felix Pfeiffer, rewritten for Contao 5.7.

## Installation

```bash
composer require vonheldenundgestalten/contao-downloadarchive
```

The package replaces `felixpfeiffer/contao-downloadarchive`, so switching only
needs the new `require`; remove the old one from your `composer.json`. The
namespace changed from `FelixPfeiffer\DownloadarchiveBundle` to
`VHUG\DownloadarchiveBundle`; tables, fields, element types and templates stay
the same.

Then run `vendor/bin/contao-console contao:migrate` (or the Contao Manager's
database update).

## Templates

| Template | Purpose |
| --- | --- |
| `content_element/downloadarchive.html.twig` | Content element |
| `frontend_module/downloadarchive.html.twig` | Front end module |
| `component/_downloadarchive.html.twig` | The list itself, shared by both |

Override them in the template studio or in your own `templates/` folder. Each
entry of `downloads` contains `href`, `file` (a `FilesystemItem`), `text`,
`description`, `archive`, `css_class`, `date`, `size`, `figure` and the raw
`item` row; see the component for details.

## Permissions

- Back end users see the archives selected in their user or group settings
  ("Download archive permissions").
- Creating and deleting archives and files is controlled by the table
  permissions of Contao 5.7 (`tl_downloadarchive`, `tl_downloadarchiveitems`).
- In the front end, protected files are only listed and downloadable for the
  selected member groups; "Guests" stands for visitors who are not logged in.

Files outside of public folders are served through signed download URLs, so a
protected file cannot be downloaded by guessing its path.

## Upgrading from the Contao 3/4 version

The database migrations take care of:

- the very old `tl_downloadarchiv*` table and `downloadarchiv` field names,
- converting the checkbox columns to boolean columns,
- "Show to guests only" on files, which becomes the "Guests" member group,
- the archive permissions (`downloadarchivep`), which move to the table
  permissions.

Breaking changes:

- The `ce_downloadarchive` / `mod_downloadarchive` PHP templates are replaced
  by the Twig templates above; custom templates have to be rewritten.
- The image margin and floating options of the preview image were removed
  (use CSS). The obsolete columns are dropped by the database update.
- Download links no longer use `?file=`; old direct links stop working.
