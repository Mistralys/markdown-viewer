# Markdown documentation viewer

PHP based viewer for Markdown files, to view them with fenced code highlighting and navigation. 

It is designed to be used for viewing markdown-based documentation files.

## Install

The project is made to be used as a dependency in your documentation project. Simply require 
it in your project:

```
composer require mistralys/markdown-viewer
```

## Quick start

1) Create the file `index.php` in your project root folder.
2) Paste the following example code into the file
3) Edit the list of files you wish to view.
4) Point your browser to the file.

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

    (new DocsViewer($manager, '/url/to/vendor'))
        ->setTitle('Documentation')
        ->display();
```

## Viewing the example

The example is built exactly like the example above, and will display the package's
`README.md` file. To get it running, follow these steps:

1) Clone the repository into a webserver's document root
2) Run `composer install` in the folder to install the dependencies
3) Point your browser to the example folder

