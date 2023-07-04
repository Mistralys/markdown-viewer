<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Parser;

use Mistralys\MarkdownViewer\DocsConfig;
use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class OptionTests extends ViewerTestCase
{
    public function test_addIncludeExtension() : void
    {
        $config = new DocsConfig();

        $this->assertNotContains('json', $config->getIncludeExtensions());
        $this->assertNotContains('rtf', $config->getIncludeExtensions());

        $config->addIncludeExtension('json');
        $config->addIncludeExtension('.rtf');

        $this->assertContains('json', $config->getIncludeExtensions());
        $this->assertContains('rtf', $config->getIncludeExtensions());
    }

    public function test_addIncludeExtensions() : void
    {
        $config = new DocsConfig();

        $this->assertNotContains('json', $config->getIncludeExtensions());

        $config->addIncludeExtensions('json', '.rtf');

        $this->assertContains('json', $config->getIncludeExtensions());
        $this->assertContains('rtf', $config->getIncludeExtensions());
    }
}
