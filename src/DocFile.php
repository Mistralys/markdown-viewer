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
        $this->id = $this->resolveID($id);
    }

    private function resolveID(string $id) : string
    {
        if(empty($id)) {
            $id = $this->path;
        }

        return ConvertHelper::string2shortHash($id);
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
