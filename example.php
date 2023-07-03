<?php

declare(strict_types=1);

use Mistralys\MarkdownViewer\DocsManager;
use Mistralys\MarkdownViewer\DocsViewer;
use Mistralys\MarkdownViewer\DocsConfig;

if(!file_exists(__DIR__.'/vendor/autoload.php')) {
    die('Please run <code>composer install</code> first.');
}

require_once __DIR__.'/vendor/autoload.php';

$config = (new DocsConfig())
    ->addIncludePath(__DIR__.'/tests/files/includes')
    ->addIncludeExtension('php');

$manager = (new DocsManager($config))
    ->addFile('Package readme', 'README.md');

(new DocsViewer($manager, 'vendor'))
    ->setTitle('Markdown viewer')
    ->setPackageURL('./')
    ->display();
