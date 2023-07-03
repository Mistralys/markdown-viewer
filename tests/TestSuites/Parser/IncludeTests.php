<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Parser;

use Mistralys\MarkdownViewer\DocParser;
use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class IncludeTests extends ViewerTestCase
{
    public function test_findIncludes() : void
    {
        $includes = $this->createTestParser('document-with-errors.md')
            ->getIncludes();

        $this->assertNotEmpty($includes);
        $this->assertArrayHasKey('not-a-file.md', $includes);
    }

    public function test_includeReplacements() : void
    {
        $html = $this->createTestParser('document-with-includes.md')
            ->render();

        $this->assertStringContainsString('sample PHP file', $html);
    }

    public function test_includeErrorTooBig() : void
    {
        $parser = $this->createTestParser('document-with-includes.md');
        $parser->getConfig()->setMaxIncludeSize(20);

        $html = $parser->render();

        $this->assertStringContainsString('#'.DocParser::ERROR_INCLUDE_FILE_TOO_BIG, $html);
    }

    public function test_includeErrors() : void
    {
        $parser = $this->createTestParser('document-with-errors.md');
        $parser->getConfig()->setMaxIncludeSize(20);

        $html = $parser->render();

        $this->assertStringContainsString('#'.DocParser::ERROR_INCLUDE_FILE_NOT_FOUND, $html, 'File not found');
        $this->assertStringContainsString('#'.DocParser::ERROR_ATTEMPTED_NAVIGATING_UP, $html, 'Navigating upwards');
        $this->assertStringContainsString('#'.DocParser::ERROR_INVALID_INCLUDE_EXTENSION, $html, 'Unknown extension');
        $this->assertStringContainsString('#'.DocParser::ERROR_INCLUDE_IS_NOT_A_FILE, $html, 'Not a file');
    }
}
