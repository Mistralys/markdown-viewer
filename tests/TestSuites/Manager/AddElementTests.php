<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestSuites\Manager;

use Mistralys\MarkdownViewer\DocsManager;
use Mistralys\MarkdownViewerTests\TestClasses\ViewerTestCase;

final class AddElementTests extends ViewerTestCase
{
    public function test_addFolder() : void
    {
        $manager = new DocsManager();
        $manager->addFolder($this->filesFolder);

        $this->assertCount(2, $manager->getFiles());
    }

    public function test_addFolderRecursive() : void
    {
        $manager = new DocsManager();
        $manager->addFolder($this->filesFolder, true);

        $this->assertCount(3, $manager->getFiles());
    }
}
