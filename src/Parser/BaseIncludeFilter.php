<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer\Parser;

use AppUtils\FileHelper\FileInfo;
use Mistralys\MarkdownViewer\DocFile;
use Mistralys\MarkdownViewer\DocsConfig;

abstract class BaseIncludeFilter
{
    abstract public function getExtensions() : array;

    /**
     * Checks whether the filter can be applied to the target
     * file, using the specified configuration.
     *
     * @param DocFile $sourceFile
     * @param DocsConfig $config
     * @param FileInfo $includeFile
     * @return bool
     */
    abstract public function isValidFor(DocFile $sourceFile, DocsConfig $config, FileInfo $includeFile) : bool;
    abstract public function filter(string $content) : string;
}
