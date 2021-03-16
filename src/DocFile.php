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
    private string $id;

    /**
     * @param string $title
     * @param string $path
     * @param string $id
     * @throws FileHelper_Exception
     */
    public function __construct(string $title, string $path, string $id='')
    {
        $this->title = $title;
        $this->path = FileHelper::requireFileExists($path);
        $this->id = $id;
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
        if(empty($this->id)) {
            $this->id = ConvertHelper::string2shortHash($this->path);
        }

        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }
}
