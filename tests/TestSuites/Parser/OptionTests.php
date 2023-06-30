<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Parser;

use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class OptionTests extends ViewerTestCase
{
    public function test_addIncludeExtension() : void
    {
        $parser = $this->createTestParser();

        $this->assertNotContains('json', $parser->getIncludeExtensions());
        $this->assertNotContains('rtf', $parser->getIncludeExtensions());

        $parser->addIncludeExtension('json');
        $parser->addIncludeExtension('.rtf');

        $this->assertContains('json', $parser->getIncludeExtensions());
        $this->assertContains('rtf', $parser->getIncludeExtensions());
    }

    public function test_addIncludeExtensions() : void
    {
        $parser = $this->createTestParser();

        $this->assertNotContains('json', $parser->getIncludeExtensions());

        $parser->addIncludeExtension('json');
        $parser->addIncludeExtension('.rtf');

        $this->assertContains('json', $parser->getIncludeExtensions());
        $this->assertContains('rtf', $parser->getIncludeExtensions());
    }
}
