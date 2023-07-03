## v1.3.0 - Include configuration update (Breaking)
- UI: Removed list of file numbers used for testing.
- Parser: Modified include behavior to require setting allowed paths.
- Parser: Increases security by disallowing including from outside allowed paths.
- Manager: Added include paths via `DocsManager::addIncludePath()`.
- Manager: Added the `DocsConfig` class to handle configuration options.

### Breaking changes

- Include files are no longer relative to the document, but to the
  configured include paths.
- Include paths must be set via the new `DocsConfig` class instead
  of the parser class.

## v1.2.0 - Include files and UI update
- Syntax: Added the `{include-file}` command to include external files.
- UI: Improved readability, max content size to improve text flow.
- UI: Fixed the list dropdown not scrolling with many files.
- UI: Started adding localization support.
- Parser: Added option to specify allowed extensions for include files.
- Parser: Added option to set max include file size.
- Unit Tests: Added first unit tests.

## v1.1.0 - PHP 7.4 and bugfix release

- Now requiring PHP >= 7.4.
- Fixed a PHP error when a document contains no headers.
- Added the `changelog.md` file.

## v1.0.6 - Maintenance release

- Fixed PHP7.3 compatibility by removing PHP7.4 specific typed properties.

## v1.0.5 - Minor improvements release

- Fixed duplicate anchor names.
- Added fenced code name aliases to support `js`, `json` or `html`.
- Reduced PHP requirement to 7.3 to support some older projects.

## v1.0.4 - Bugfix release

- Fixed code fences other than PHP not being recognized correctly, causing overlapping code blocks.

## v1.0.3 - Minor feature release

- Added the dark mode layout.
- Added the optional file IDs for consistent permalinks.
- Added permalink icons to all headers.

## v1.0.2 - Minor feature release

- Added the viewer's `addFolder()` method.
- Added dark mode support.
- Fixed indented "1)" style bullets not being detected properly.

## v1.0.1 - Layout tweaks release

- Tweaked the sidebar padding slightly.
- Added `setMenuLabel()` to the viewer.
- Changed the documents menu label to "Available documents".

## v1.0.0 - Initial featureset release

- Release with all basic functionality as initially planned.