<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Parser;

use Mistralys\MarkdownViewer\DocParser;
use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class IncludeTests extends ViewerTestCase
{
    public function test_findIncludes() : void
    {
        $includes = $this->createTestParser('document-with-includes.md')
            ->getIncludes();

        $this->assertNotEmpty($includes);
        $this->assertArrayHasKey('includes/test-php-highlight.php', $includes);
    }

    public function test_includeReplacements() : void
    {
        $html = $this->createTestParser('document-with-includes.md')
            ->render();

        $this->assertStringContainsString('sample PHP file', $html);
        $this->assertStringContainsString('#'.DocParser::ERROR_INCLUDE_FILE_NOT_FOUND, $html);
    }

    public function test_includeSizeTooBig() : void
    {
        $html = $this->createTestParser('document-with-includes.md')
            ->setMaxIncludeSize(20)
            ->render();

        $this->assertStringContainsString('#'.DocParser::ERROR_INCLUDE_FILE_TOO_BIG, $html);
    }
}
