# Markdown documentation viewer

PHP based viewer for Markdown files, to view them with fenced code highlighting and navigation. 

It is designed to be used for viewing markdown-based documentation files, in a fire and forget
way. The layout is based on [Bootstrap 5](https://getbootstrap.com), and does not need any 
additional configuration.

## Requirements

- PHP7.4+

## Features

- Automatic jump navigation built using the document's headers.
- Easily switch between the available documents.
- Syntax highlighted fenced code blocks.
- Light and dark modes. 
- Additional support for "1)" style ordered lists.

## Installing

The package is made to be used as a dependency in a documentation project:
Put it in a folder in a webserver, point it to some markdown files, and it
will display them.

1) Create a folder in your webroot from which to serve the documentation.
2) Create a composer project there.
3) Require the package: `composer require mistralys/markdown-viewer`.
4) Create a PHP file (`index.php`) as endpoint for the documentation.
5) Paste the following code into the file
6) Edit the list of files you wish to view.
7) Point your browser to the file.

```php
<?php

declare(strict_types=1);

use Mistralys\MarkdownViewer\DocsManager;
use Mistralys\MarkdownViewer\DocsViewer;

if(!file_exists(__DIR__.'/vendor/autoload.php')) {
    die('Please run <code>composer install</code> first.');
}

require_once __DIR__.'/vendor/autoload.php';

$manager = new DocsManager();

// Add all the files you wish to view here, along with
// a title that will be shown in the UI.
$manager->addFile('Title of the file', '/path/to/documentation.md');

// The viewer needs to know the URL to the vendor/ folder, relative
// to the script. This is needed to load the clientside dependencies,
// like jQuery and Bootstrap.
(new DocsViewer($manager, '/url/to/vendor'))
    ->setTitle('Documentation')
    ->display();
```

## Adding single files

Single files can be added using `addFile()`. This allows specifying the
name that the file will be listed under in the UI.

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add a single folder, non-recursive.
$manager->addFile('Name of the file', '/path/to/file.md');
```

## Adding folders

To add multiple files, use the `addFolder()` method:

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add a single folder, non-recursive.
$manager->addFolder('/path/to/files');

// Add a folder and all its subfolders
$manager->addFolder('/path/to/files', true);
```

By default, all files with the `md` extension will be added. A different extension
can be specified using the third parameter:

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add all TXT files from a single folder, non-recursive.
$manager->addFolder('/path/to/files', false, 'txt');
```

  > NOTE: Adding files this way means you cannot specify file IDs (see "Consistent 
    file permalinks"). Please double-check that this is okay in your use case.

## Consistent file permalinks

By default, the viewer will create an ID for each file based on its absolute
path on disk. This means that the ID will change if the file is moved at some
point, or if the viewer is used on different systems. Sharing permalinks risks
the links being broken at some point.

To avoid this issue, specify a unique file ID manually when adding single files:

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

$manager->addFile(
    'Name of the file', 
    '/path/to/file.md',
    '(Unique file ID)'
);
```

The ID can be any string; the viewer uses it to create the hash that is used in the UI
to identify the files. This way, permalinks will always stay consistent. 

## Dark mode

To turn on dark mode, simply use `makeDarkMode()`:

```php
use Mistralys\MarkdownViewer\DocsViewer;
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Configure the files

(new DocsViewer($manager, '/url/to/vendor'))
    ->setTitle('Documentation')
    ->makeDarkMode()
    ->display();
```

## Viewing the example

The bundled example is built exactly like the example above, and will display 
this `README.md` file. To get it running, follow these steps:

1) Clone the repository into a webserver's document root
2) Run `composer install` in the package folder to install the dependencies
3) Point your browser to the package folder's `example.php` file

## Setting options

All options regarding the parsing of markdown files are handled by the `DocsConfig`
class. You can optionally pass a configuration instance to the manager to customize
settings:

```php
use Mistralys\MarkdownViewer\DocsManager;
use Mistralys\MarkdownViewer\DocsConfig;

$config = (new DocsConfig())
    ->addIncludePath(__DIR__.'/documentation/includes')
    ->addIncludeExtension('php');

$manager = (new DocsManager($config))
    ->addFile('Package readme', 'README.md');
```

## Including external files

### The include command

The `{include-file}` command allows you to import the content of external files
into your documents. This is especially handy for code examples, as it allows
you to maintain them separately from the main document.

If viewed through `example.php`, the following code sample is loaded dynamically, 
for example:

```php
{include-file: test-php-highlight.php}
```

The command looks like this:

```
\{include-file: test-php-highlight.php\}
```

> NOTE: It is easy to go overboard with includes. Keep in mind that Markdown files
> are meant to be read as-is. Splitting them up too much will make them unreadable
> without the UI. Use them where it makes sense, like for large code samples.

### Setting allowed paths

Include commands are disallowed by default, as long as no include folders have
been configured:

```php
use Mistralys\MarkdownViewer\DocsConfig;

$config = (new DocsConfig())
    ->addIncludePath('/documentation/includes');
```

Paths in the `{include-file}` command are relative to the configured include paths. 
Multiple folders can be added, and all of them are searched. The first matching file 
name is then used.

### Setting allowed extensions

By default, **only `md` and `txt` files are allowed** to be included. Additional 
extensions can easily be added:

```php
use Mistralys\MarkdownViewer\DocsConfig;

$config = (new DocsConfig())
    ->addIncludeExtension('php');
```

Several extensions can also be added at once:

```php
use Mistralys\MarkdownViewer\DocsConfig;

$extensions = array(
    'php',
    'js',
    'css'
);

$config = (new DocsConfig())
    ->addIncludeExtensions($extensions);
```

### Restricting file sizes

To avoid including large files, only **files up to 6Kb may be included** by default. 
This can be adjusted with the configuration class:

```php
use Mistralys\MarkdownViewer\DocsConfig;

// Allow files up to 12Kb (12.000 bytes)
$config = (new DocsConfig())
    ->setMaxIncludeSize(12000);
```
