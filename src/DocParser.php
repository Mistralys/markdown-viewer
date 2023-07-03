<?php
/**
 * File containing the class {@see \Mistralys\MarkdownViewer\DocParser}.
 *
 * @package MarkdownViewer
 * @see \Mistralys\MarkdownViewer\DocParser
 */

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\FileHelper\FileInfo;
use AppUtils\FileHelper_Exception;
use GeSHi;
use ParsedownExtra;
use PHPUnit\TextUI\XmlConfiguration\File;

/**
 * Markdown document parser. Uses "ParseDown extra" to parse the
 * document, and adds some of its own functionality into the mix.
 *
 * @package MarkdownViewer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class DocParser
{
    public const ERROR_INCLUDE_FILE_NOT_FOUND = 140801;
    public const ERROR_INCLUDE_IS_NOT_A_FILE = 140802;
    public const ERROR_INCLUDE_FILE_TOO_BIG = 140803;
    public const ERROR_ATTEMPTED_NAVIGATING_UP = 140804;
    public const ERROR_INVALID_INCLUDE_EXTENSION = 140805;

    public const OVERALL_CLASS = 'geshifilter';

    private ?string $html = null;
    private DocFile $file;

    /**
     * @var DocHeader[]
     */
    private array $headers = array();

    /**
     * Aliases for code fence language names.
     *
     * @var array<string,string>
     */
    private array $langAliases = array(
        'js' => 'javascript',
        'html' => 'html5',
        'json' => 'javascript'
    );

    /**
     * @var array<string,array<int,string>>
     */
    private array $includes = array();
    private DocsConfig $config;

    public function __construct(DocFile $file)
    {
        $this->file = $file;
        $this->config = $this->file->getManager()->getConfiguration();
    }

    public function getConfig(): DocsConfig
    {
        return $this->config;
    }

    private function parse() : void
    {
        if(isset($this->html)) {
            return;
        }

        $parse = new ParsedownExtra();

        $text = FileHelper::readContents($this->file->getPath());
        $text = $this->preParseListStyles($text);
        $text = $this->preParseDetectIncludeFiles($text);
        $text = $this->preParseInjectIncludes($text);

        $this->html = (string)$parse->text($text);

        $this->parseHeaders();
        $this->parseCode();
    }

    /**
     * @return DocHeader[]
     */
    public function getHeaders() : array
    {
        $this->parse();

        return $this->headers;
    }

    /**
     * Replace all list items in the text that use the "1)" notation
     * with the markdown "1." style, so it can be detected correctly.
     *
     * @param string $text
     * @return string
     */
    private function preParseListStyles(string $text) : string
    {
        $lines = explode("\n", $text);

        $keep = array();
        foreach ($lines as $line)
        {
            $pos = strpos($line, ')');

            if($pos !== false) {
                $line = $this->checkBullet($line, $pos);
            }

            $keep[] = $line;
        }

        return implode("\n", $keep);
    }

    private function checkBullet(string $line, int $pos) : string
    {
        $sub = substr($line, 0, $pos);
        $trimmed = trim($sub);

        if(is_numeric($trimmed)) {
            return substr_replace($line,$trimmed.'.', 0, $pos+1);
        }

        return $line;
    }

    private function preParseDetectIncludeFiles(string $text) : string
    {
        if(stripos($text, '{include-file:') === false) {
            return $text;
        }

        preg_match_all('/{include-file:([^{}]+)}/iU', $text, $matches);

        if(isset($matches[0][0]))
        {
            foreach($matches[0] as $idx => $matchedText)
            {
                $file = trim($matches[1][$idx]);
                if(!isset($this->includes[$file])) {
                    $this->includes[$file] = array();
                }

                if(!in_array($matchedText, $this->includes[$file], true)) {
                    $this->includes[$file][] = $matchedText;
                }
            }
        }

        return $text;
    }

    private function preParseInjectIncludes(string $text) : string
    {
        $replaces = array();

        $text = str_replace(
            array(
                '\{',
                '\}'
            ),
            array(
                '§__BRACKET_OPEN__§',
                '§__BRACKET_CLOSE__§'
            ),
            $text
        );

        foreach($this->includes as $relativePath => $textMatches)
        {
            $content = $this->renderIncludeContent($relativePath);

            foreach($textMatches as $matchedText)
            {
                $replaces[$matchedText] = $content;
            }
        }

        $text = str_replace(
            array_keys($replaces),
            array_values($replaces),
            $text
        );

        // Restore escaped brackets
        return str_replace(
            array(
                '§__BRACKET_OPEN__§',
                '§__BRACKET_CLOSE__§'
            ),
            array(
                '{',
                '}'
            ),
            $text
        );
    }

    /**
     * Detects the include file to load from the relative path
     * specified in the document (looks in all include folders
     * specified with {@see self::addIncludePath()}), disallowing
     * paths that navigate upwards with <code>../</code>.
     *
     * @param string $relativePath
     * @return FileInfo
     *
     * @throws DocsException
     * @throws FileHelper_Exception
     */
    public function findIncludeFile(string $relativePath) : FileInfo
    {
        // Disallow navigating upwards with "../"
        if(strpos($relativePath, '..') !== false) {
            throw new DocsException(
                'Navigating upwards from include folders is not allowed.',
                '',
                self::ERROR_ATTEMPTED_NAVIGATING_UP
            );
        }

        $ext = FileHelper::getExtension($relativePath);

        if(!in_array($ext, $this->config->getIncludeExtensions(), true)) {
            throw new DocsException(
                sprintf('The extension [%s] is not allowed.', $ext),
                '',
                self::ERROR_INVALID_INCLUDE_EXTENSION
            );
        }

        $paths = $this->config->getIncludePaths();

        foreach($paths as $path)
        {
            $absolute = $path->getPath().'/'.$relativePath;

            if(!file_exists($absolute)) {
                continue;
            }

            if(is_dir($absolute)) {
                throw new DocsException(
                    sprintf(
                        'Include path `%s` is not a file.',
                        basename($absolute)
                    ),
                    '',
                    self::ERROR_INCLUDE_IS_NOT_A_FILE
                );
            }

            if(filesize($absolute) > $this->config->getMaxIncludeSize()) {
                throw new DocsException(
                    sprintf(
                        'Include file `%s` is too big. Max file size is %s.',
                        basename($absolute),
                        ConvertHelper::bytes2readable($this->config->getMaxIncludeSize())
                    ),
                    '',
                    self::ERROR_INCLUDE_FILE_TOO_BIG
                );
            }

            return FileInfo::factory($absolute);
        }

        throw new DocsException(
            sprintf(
                'Include file `%s` not found.',
                basename($relativePath)
            ),
            '',
            self::ERROR_INCLUDE_FILE_NOT_FOUND
        );
    }

    private function renderIncludeContent(string $relativePath) : string
    {
        try{
            return $this->filterInclude($this->findIncludeFile($relativePath));
        }
        catch (DocsException $e)
        {
            return $this->renderErrorMessage(
                $e->getMessage(),
                $e->getCode()
            );
        }
    }

    private function filterInclude(FileInfo $file) : string
    {
        $content = $file->getContents();
        $filters = $this->config->getIncludeFilters($file->getExtension());

        foreach($filters as $filter)
        {
            if($filter->isValidFor($this->file, $this->config, $file)) {
                $content = $filter->filter($content);
            }
        }

        return $content;
    }

    private function renderErrorMessage(string $message, int $code) : string
    {
        return sprintf('**Error #%s:** %s', $code, $message).PHP_EOL;
    }

    /**
     * Returns all detected include files. The keys are the
     * relative paths to the target file, the values are indexed
     * array with the matched <code>{include}</code> commands.
     *
     * @return array<string,array<int,string>>
     */
    public function getIncludes() : array
    {
        $this->parse();

        return $this->includes;
    }

    private function parseHeaders() : void
    {
        preg_match_all('%<h([0-9])\b[^>]*>(.*?)</h[0-9]>%si', $this->html, $result, PREG_PATTERN_ORDER);

        $active = array();
        $headers = array();

        foreach($result[2] as $idx => $title)
        {
            $header = new DocHeader($title, (int)$result[1][$idx], $result[0][$idx]);

            $level = $header->getLevel();

            $headers[] = $header;
            $active[$level] = $header;

            if($level === 1) {
                $this->headers[] = $header;
                continue;
            }

            $prevIndex = ($level-1);

            if(isset($active[$prevIndex]))
            {
                $active[$prevIndex]->addSubheader($header);
            }
        }

        foreach ($headers as $header)
        {
            $this->html = $header->replace($this->html, $this->file);
        }
    }

    private function parseCode() : void
    {
        preg_match_all('%<code class="language-([a-z]+)">(.*?)</code>%si', $this->html, $result, PREG_PATTERN_ORDER);

        foreach($result[2] as $idx => $matchedText)
        {
            $this->html = str_replace($matchedText, $this->renderCode($matchedText, $this->resolveLanguage($result[1][$idx])), $this->html);
        }
    }

    private function resolveLanguage(string $lang) : string
    {
        return $this->langAliases[$lang] ?? $lang;
    }

    private function renderCode(string $code, string $language) : string
    {
        $code = html_entity_decode($code);

        $geshi = new GeSHi($code, $language);
        $geshi->set_overall_class(self::OVERALL_CLASS);
        $geshi->enable_classes();
        $geshi->set_methods_highlighting(true);
        $geshi->set_numbers_highlighting(true);
        $geshi->set_symbols_highlighting(true);
        $geshi->set_strings_highlighting(true);
        $high = $geshi->parse_code();

        return str_replace(
            array(
                sprintf('<pre class="%s %s">', $language, self::OVERALL_CLASS),
                '</pre>'
            ),
            '',
            $high
        );
    }

    public function render() : string
    {
        $this->parse();

        return $this->html;
    }

    // region: Options





    // endregion
}
