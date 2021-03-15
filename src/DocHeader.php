<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

use AppUtils\ConvertHelper;

class DocHeader
{
    private string $title;
    private int $level;
    private string $tag;
    private string $id;

    /**
     * @var DocHeader[]
     */
    private array $headers = array();

    public function __construct(string $title, int $level, string $matchedTag)
    {
        $this->title = $title;
        $this->level = $level;
        $this->tag = $matchedTag;
        $this->id = ConvertHelper::transliterate($title);
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

    /**
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    public function replace(string $subject) : string
    {
        $new = str_replace(
            sprintf('<h%s>', $this->level),
            sprintf('<h%s><a class="anchor" id="%s"></a>', $this->level, $this->id),
            $this->tag
        );

        return substr_replace($subject, $new, strpos($subject, $this->tag), strlen($this->tag));
    }

    public function render() : string
    {
        ob_start();

        ?>
            <li>
                <a href="#<?php echo $this->id ?>"><?php echo $this->title ?></a>
                <?php echo $this->renderSubheaders() ?>
            </li>
        <?php

        return ob_get_clean();
    }

    private function renderSubheaders() : string
    {
        if(empty($this->headers)) {
            return '';
        }

        ob_start();
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

        return ob_get_clean();
    }
}