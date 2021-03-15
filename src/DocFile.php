<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper_Exception;

class DocFile
{
    private string $title;
    private string $path;

    /**
     * @param string $title
     * @param string $path
     * @throws FileHelper_Exception
     */
    public function __construct(string $title, string $path)    
    {
        $this->title = $title;
        $this->path = FileHelper::requireFileExists($path);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function getID() : string
    {
        return ConvertHelper::string2shortHash($this->path);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
