<?php
/**
 * File containing the class {@see \Mistralys\MarkdownViewer\DocsManager}.
 *
 * @package MarkdownViewer
 * @see \Mistralys\MarkdownViewer\DocsManager
 */

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\FileHelper;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper_Exception;
use SplFileInfo;

/**
 * Handles a collection of documentation files to use
 * in the viewer. Handles registering and reading the
 * list of files.
 *
 * @package MarkdownViewer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class DocsManager
{
    public const ERROR_NO_FIRST_FILE_FOUND = 82101;
    public const ERROR_UNKNOWN_FILE_ID = 82102;

    /**
     * @var DocFile[]
     */
    private array $files = array();
    private ?DocsConfig $config;

    public function __construct(?DocsConfig $config=null)
    {
        if($config === null) {
            $config = new DocsConfig();
        }

        $this->config = $config;
    }

    public function getConfiguration() : DocsConfig
    {
        return $this->config;
    }

    /**
     * Adds a documentation file to the collection.
     *
     * @param string $title
     * @param string $path
     * @param string $id Optional ID to uniquely identify the file. If empty, the path is used. Specify an ID if you wish file IDs to be the same cross-system.
     * @return $this
     * @throws FileHelper_Exception
     */
    public function addFile(string $title, string $path, string $id='') : DocsManager
    {
        $this->files[] = new DocFile($this, $title, $path, $id);
        return $this;
    }

    /**
     * Adds files from a folder.
     *
     * @param string $path
     * @param bool $recursive
     * @param string $extension
     * @return $this
     * @throws FileHelper_Exception
     */
    public function addFolder(string $path, bool $recursive=false, string $extension='md') : DocsManager
    {
        FileHelper::requireFolderExists($path);

        $finder = FileHelper::createFileFinder($path)
            ->includeExtension($extension)
            ->setPathmodeAbsolute();

        if($recursive) {
            $finder->makeRecursive();
        }

        $files = $finder->getAll();

        foreach ($files as $file)
        {
            $this->addFile(FileHelper::removeExtension($file), $file);
        }

        return $this;
    }

    public function hasFiles() : bool
    {
        return !empty($this->files);
    }

    /**
     * Retrieves a documentation file by its ID.
     *
     * @param string $id
     * @return DocFile
     * @throws DocsException
     */
    public function getByID(string $id) : DocFile
    {
        foreach($this->files as $file) {
            if($file->getID() === $id) {
                return $file;
            }
        }

        throw new DocsException(
            'No such documentation file found.',
            sprintf('Tried accessing document by ID [%s].', $id),
            self::ERROR_UNKNOWN_FILE_ID
        );
    }

    public function idExists(string $id) : bool
    {
        foreach($this->files as $file) {
            if($file->getID() === $id) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return DocFile
     * @throws DocsException
     *
     * @see DocsManager::ERROR_NO_FIRST_FILE_FOUND
     */
    public function getFirstFile() : DocFile
    {
        if(isset($this->files[0])) {
            return $this->files[0];
        }

        throw new DocsException(
            'No documents available.',
            'Tried fetching the first file in the collection, but there are no files at all.',
            self::ERROR_NO_FIRST_FILE_FOUND
        );
    }

    public function getFiles() : array
    {
        usort($this->files, static function (DocFile $a, DocFile $b) : int {
            return strnatcasecmp($a->getTitle(), $b->getTitle());
        });

        return $this->files;
    }
}
