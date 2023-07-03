<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewerTests\TestClasses;

use AppUtils\FileHelper\FileInfo;
use Mistralys\MarkdownViewer\DocFile;
use Mistralys\MarkdownViewer\DocsConfig;
use Mistralys\MarkdownViewer\Parser\BaseIncludeFilter;

class IncludeFilterTest extends BaseIncludeFilter
{
    public const REPLACED_CONTENT = '(replaced content)';

    public function getExtensions(): array
    {
        return array('php');
    }

    public function filter(DocFile $sourceFile, DocsConfig $config, FileInfo $includeFile, string $content): string
    {
        return self::REPLACED_CONTENT;
    }
}
