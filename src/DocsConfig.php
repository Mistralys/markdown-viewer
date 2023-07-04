<?php
/**
 * @package MarkdownViewer
 * @see \Mistralys\MarkdownViewer\DocsConfig
 */

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper_Exception;
use Mistralys\MarkdownViewer\Parser\BaseIncludeFilter;
use SplFileInfo;

/**
 * Handles configuration options for parsing Markdown files.
 *
 * @package MarkdownViewer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class DocsConfig
{
    public const ERROR_FILTER_WITHOUT_EXTENSIONS = 141001;

    /**
     * @var array<string,FolderInfo>
     */
    private array $includePaths = array();

    /**
     * @var array<string,array<int,BaseIncludeFilter>>
     */
    private array $filters = array();

    /**
     * @var string[]
     */
    private array $includeExtensions = array(
        'md',
        'txt'
    );

    private int $maxIncludeSize = 6000;

    /**
     * @param string|FolderInfo|SplFileInfo $path
     * @return $this
     * @throws FileHelper_Exception
     */
    public function addIncludePath($path) : self
    {
        $path = FolderInfo::factory($path)->requireExists();
        $normalized = $path->getPath();

        if(!isset($this->includePaths[$normalized])) {
            $this->includePaths[$normalized] = $path;
        }

        return $this;
    }

    /**
     * @return FolderInfo[]
     */
    public function getIncludePaths() : array
    {
        return array_values($this->includePaths);
    }

    /**
     * Adds a file extension to allow for include files.
     *
     * @param string $extension
     * @return $this
     */
    public function addIncludeExtension(string $extension) : self
    {
        $extension = strtolower(ltrim($extension, '.'));

        if(!in_array($extension, $this->includeExtensions, true)) {
            $this->includeExtensions[] = $extension;
        }

        return $this;
    }

    public function addIncludeExtensions(...$extensions) : self
    {
        foreach($extensions as $extension)
        {
            if(is_array($extension)) {
                $this->addIncludeExtensions(...$extension);
                continue;
            }

            $this->addIncludeExtension($extension);
        }

        return $this;
    }

    public function getIncludeExtensions() : array
    {
        return $this->includeExtensions;
    }

    public function setMaxIncludeSize(int $bytes) : self
    {
        $this->maxIncludeSize = $bytes;
        return $this;
    }

    public function getMaxIncludeSize() : int
    {
        return $this->maxIncludeSize;
    }

    /**
     * Adds a filtering callback to modify the content loaded from an include
     * file before it is inserted in the document.
     *
     * @param BaseIncludeFilter $filter
     * @return $this
     * @throws DocsException {@see self::ERROR_FILTER_WITHOUT_EXTENSIONS}
     */
    public function addIncludeFilter(BaseIncludeFilter $filter) : self
    {
        $extensions = $filter->getExtensions();

        if(empty($extensions)) {
            throw new DocsException(
                'The filter has no extensions configured.',
                sprintf(
                    'Target filter class: [%s].',
                    get_class($filter)
                ),
                self::ERROR_FILTER_WITHOUT_EXTENSIONS
            );
        }

        foreach($extensions as $extension)
        {
            if (!isset($this->filters[$extension])) {
                $this->filters[$extension] = array();
            }

            $this->filters[$extension][] = $filter;
        }

        return $this;
    }

    public function getIncludeFilters(string $extension) : array
    {
        return $this->filters[$extension] ?? array();
    }
}
