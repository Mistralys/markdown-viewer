<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Parser;

use Mistralys\MarkdownViewerTests\TestClasses\IncludeFileIgnoreFilterTest;
use Mistralys\MarkdownViewerTests\TestClasses\IncludeFilterTest;
use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class IncludeFilterTests extends ViewerTestCase
{
    public function test_findIncludes(): void
    {
        $parser = $this->createTestParser('document-with-includes.md');
        $config = $parser->getConfig();

        $config->addIncludeFilter(new IncludeFilterTest());

        $this->assertStringContainsString(IncludeFilterTest::REPLACED_CONTENT, $parser->render());
    }

    public function test_ignoreIncludesII() : void
    {
        $parser = $this->createTestParser('document-with-includes.md');

        $config = $parser->getConfig();
        $config->addIncludeFilter(new IncludeFileIgnoreFilterTest());

        $html = $parser->render();

        $this->assertStringContainsString('sampleFunction', $html);
        $this->assertStringNotContainsString(IncludeFileIgnoreFilterTest::REPLACED_CONTENT, $html);
    }
}
