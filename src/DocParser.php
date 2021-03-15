<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\ConvertHelper;
use AppUtils\FileHelper;
use AppUtils\Highlighter;
use ParsedownExtra;

class DocParser
{
    /**
     * @var DocHeader[]
     */
    private array $headers = array();

    private string $html;

    public function __construct(DocFile $file)
    {
        $parse = new ParsedownExtra();

        $text = FileHelper::readContents($file->getPath());
        $text = $this->parseListStyles($text);

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

        foreach($result[2] as $idx => $title)
        {
            $header = new DocHeader($title, intval($result[1][$idx]), $result[0][$idx]);

            $level = $header->getLevel();

            $active[$level] = $header;

            $this->html = $header->replace($this->html);

            if($level === 1) {
                $this->headers[] = $header;
                continue;
            }

            $active[($level-1)]->addSubheader($header);
        }
    }

    private function parseCode() : void
    {
        preg_match_all('%<code class="language-([a-z]+)">(.*?)</code>%si', $this->html, $result, PREG_PATTERN_ORDER);

        foreach($result[2] as $idx => $matchedText)
        {
            $this->html = str_replace($matchedText, $this->renderCode($matchedText, $result[1][$idx]), $this->html);
        }
    }

    private function renderCode(string $code, string $language) : string
    {
        $code = html_entity_decode($code);

        $high = Highlighter::fromString($code, $language)->parse_code();

        $high = str_replace('<pre class="php" style="font-family:monospace;">', '', $high);
        $high = str_replace('</pre>', '', $high);

        return $high;
    }

    public function render() : string
    {
        return $this->html;
    }
}
