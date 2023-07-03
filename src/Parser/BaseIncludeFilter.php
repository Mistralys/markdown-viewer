<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer\Parser;

use AppUtils\FileHelper\FileInfo;
use Mistralys\MarkdownViewer\DocFile;
use Mistralys\MarkdownViewer\DocsConfig;

abstract class BaseIncludeFilter
{
    abstract public function getExtensions() : array;
    abstract public function filter(DocFile $sourceFile, DocsConfig $config, FileInfo $includeFile, string $content) : string;
}
