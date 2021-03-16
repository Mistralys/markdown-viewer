# Markdown documentation viewer

PHP based viewer for Markdown files, to view them with fenced code highlighting and navigation. 

It is designed to be used for viewing markdown-based documentation files, in a fire and forget
way. The layout is based on Bootstrap 4, and does not need any additional configuration.

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

if(!file_exists('vendor/autoload.php')) {
    die('Please run <code>composer install</code> first.');
}

require_once 'vendor/autoload.php';

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

## Adding files

Single files can be added using `addFile()`. This allows specifying the
name that the file will be listed under in the UI.

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add a single folder, non recursive.
$manager->addFile('Name of the file', '/path/to/file.md');
```

To add multiple files, use the `addFolder()` method:

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add a single folder, non recursive.
$manager->addFolder('/path/to/files');

// Add a folder and all its subfolders
$manager->addFolder('/path/to/files', true);
```

By default, all files with the `md` extension will be added. A different extension
can be specified using the third parameter:

```php
use Mistralys\MarkdownViewer\DocsManager;

$manager = new DocsManager();

// Add all TXT files from a single folder, non recursive.
$manager->addFolder('/path/to/files', false, 'txt');
```

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
