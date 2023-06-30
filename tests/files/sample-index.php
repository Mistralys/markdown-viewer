<?php

declare(strict_types=1);

use Mistralys\MarkdownViewer\DocsManager;
use Mistralys\MarkdownViewer\DocsViewer;

if(!file_exists('vendor/autoload.php')) {
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
