<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestClasses;

use Mistralys\MarkdownViewer\DocFile;
use Mistralys\MarkdownViewer\DocParser;
use Mistralys\MarkdownViewer\DocsConfig;
use Mistralys\MarkdownViewer\DocsManager;
use PHPUnit\Framework\TestCase;

abstract class ViewerTestCase extends TestCase
{
    protected string $filesFolder;

    protected function setUp(): void
    {
        $this->filesFolder = __DIR__.'/../files';
    }

    protected function getPath(string $relativePath) : string
    {
        $path = $this->filesFolder.'/'.ltrim($relativePath, '/');

        $this->assertFileExists($path);

        return $path;
    }

    protected function createTestParser(string $relativePath='', ?string $title='') : DocParser
    {
        if(empty($relativePath)) {
            $relativePath = 'document-with-includes.md';
        }

        if(empty($title)) {
            $title = 'Title';
        }

        $config = (new DocsConfig())
            ->addIncludePath($this->filesFolder.'/includes')
            ->addIncludeExtension('php');

        $manager = new DocsManager($config);

        return new DocParser(new DocFile($manager, $title, $this->getPath($relativePath)));
    }
}
