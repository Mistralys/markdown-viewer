<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\FileHelper;
use GeSHi;
use ParsedownExtra;

class DocParser
{
    /**
     * @var DocHeader[]
     */
    private $headers = array();

    /**
     * @var string
     */
    private $html;

    /**
     * @var DocFile
     */
    private $file;

    /**
     * Aliases for code fence language names.
     *
     * @var array<string,string>
     */
    private $langAliases = array(
        'js' => 'javascript',
        'html' => 'html5',
        'json' => 'javascript'
    );

    public function __construct(DocFile $file)
    {
        $parse = new ParsedownExtra();

        $text = FileHelper::readContents($file->getPath());
        $text = $this->parseListStyles($text);

        $this->file = $file;
        $this->html = strval($parse->text($text));

        $this->parseHeaders();
        $this->parseCode();
    }

    /**
     * @return DocHeader[]
     */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    /**
     * Replace all list items in the text that use the "1)" notation
     * with the markdown "1." style so it can be detected correctly.
     *
     * @param string $text
     * @return string
     */
    private function parseListStyles(string $text) : string
    {
        $lines = explode("\n", $text);
        $total = count($lines);

        $keep = array();
        for($i=0; $i < $total; $i++)
        {
            $line = $lines[$i];
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

    private function parseHeaders() : void
    {
        preg_match_all('%<h([0-9])\b[^>]*>(.*?)</h[0-9]>%si', $this->html, $result, PREG_PATTERN_ORDER);

        $active = array();
        $headers = array();

        foreach($result[2] as $idx => $title)
        {
            $header = new DocHeader($title, intval($result[1][$idx]), $result[0][$idx]);

            $level = $header->getLevel();

            $headers[] = $header;
            $active[$level] = $header;

            if($level === 1) {
                $this->headers[] = $header;
                continue;
            }

            $active[($level-1)]->addSubheader($header);
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
        if(isset($this->langAliases[$lang])) {
            return $this->langAliases[$lang];
        }

        return $lang;
    }

    private function renderCode(string $code, string $language) : string
    {
        $code = html_entity_decode($code);

        $geshi = new GeSHi($code, $language);
        $geshi->set_overall_class('geshifilter');
        $geshi->enable_classes();
        $geshi->set_methods_highlighting(true);
        $geshi->set_numbers_highlighting(true);
        $geshi->set_symbols_highlighting(true);
        $geshi->set_strings_highlighting(true);
        $high = $geshi->parse_code();

        $high = str_replace('<pre class="'.$language.' geshifilter">', '', $high);
        $high = str_replace('</pre>', '', $high);

        return $high;
    }

    public function render() : string
    {
        return $this->html;
    }
}
