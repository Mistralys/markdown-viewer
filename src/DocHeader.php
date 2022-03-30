<?php
/**
 * File containing the class {@see \Mistralys\MarkdownViewer\DocHeader}.
 *
 * @package MarkdownViewer
 * @see \Mistralys\MarkdownViewer\DocHeader
 */

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\ConvertHelper;
use AppUtils\OutputBuffering;

/**
 * Handles a single header in the document, with
 * information on the anchor to use to jump to it,
 * among other things.
 *
 * @package MarkdownViewer
 * @author Sebastian Mordziol <s.mordziol@mistralys.eu>
 */
class DocHeader
{
    private string $title;
    private int $level;
    private string $tag;
    private string $id;
    private string $anchor;

    /**
     * @var DocHeader[]
     */
    private array $headers = array();

    /**
     * @var array<string,int>
     */
    private static array $anchors = array();

    public function __construct(string $title, int $level, string $matchedTag)
    {
        $this->title = $title;
        $this->level = $level;
        $this->tag = $matchedTag;
        $this->id = ConvertHelper::transliterate($title);
        $this->anchor = $this->createAnchor();
    }

    private function createAnchor() : string
    {
        if(!isset(self::$anchors[$this->id])) {
            self::$anchors[$this->id] = 0;
        }

        self::$anchors[$this->id]++;

        $anchor = $this->id;

        if(self::$anchors[$this->id] > 1) {
            $anchor .= '-'.self::$anchors[$this->id];
        }

        return $anchor;
    }

    public function addSubheader(DocHeader $header) : void
    {
        $this->headers[] = $header;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->id;
    }

    public function getAnchor() : string
    {
        return $this->anchor;
    }

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Replaces the <hx> tag in the subject string with the adjusted
     * markup for the permalink icon and jump anchor.
     *
     * @param string $subject
     * @param DocFile $file
     * @return string
     */
    public function replace(string $subject, DocFile $file) : string
    {
        $template =
            '<h%1$s>'.
                '<a class="permalink" href="?doc=%3$s#%2$s">'.
                    '<span>§</span>'.
                '</a>'.
                '<a class="anchor" id="%2$s"></a>';

        $new = str_replace(
            sprintf('<h%s>', $this->level),
            sprintf(
                $template,
                $this->level,
                $this->getAnchor(),
                $file->getID()
            ),
            $this->tag
        );

        return substr_replace($subject, $new, strpos($subject, $this->tag), strlen($this->tag));
    }

    public function render() : string
    {
        OutputBuffering::start();

        ?>
            <li>
                <a href="#<?php echo $this->getAnchor() ?>"><?php echo $this->title ?></a>
                <?php echo $this->renderSubheaders() ?>
            </li>
        <?php

        return OutputBuffering::get();
    }

    private function renderSubheaders() : string
    {
        if(empty($this->headers)) {
            return '';
        }

        OutputBuffering::start();
        ?>
            <ul class="nav-level-<?php echo $this->level ?>">
                <?php
                    foreach ($this->headers as $header)
                    {
                        echo $header->render();
                    }
                ?>
            </ul>
        <?php

        return OutputBuffering::get();
    }
}
