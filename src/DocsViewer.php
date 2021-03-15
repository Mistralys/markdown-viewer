<?php

declare(strict_types=1);

namespace Mistralys\MarkdownViewer;

class DocsViewer
{
    const ERROR_NO_DOCUMENTS_AVAILABLE = 82001;

    private string $title = 'Documentation';

    private DocsManager $docs;

    private string $vendorURL;

/**
     * @param DocsManager $manager
     * @throws DocsException
     *
     * @see DocsViewer::ERROR_NO_DOCUMENTS_AVAILABLE
     */
    public function __construct(DocsManager $manager, string $vendorURL)
    {
        $this->docs = $manager;
        $this->vendorURL = $vendorURL;

        if(!$this->docs->hasFiles()) {
            throw new DocsException(
                'Cannot start viewer, the are no documents to display.',
                '',
                self::ERROR_NO_DOCUMENTS_AVAILABLE
            );
        }
    }

    public function setTitle(string $title) : DocsViewer
    {
        $this->title = $title;
        return $this;
    }

    public function getActiveFileID() : string
    {
        if(isset($_REQUEST['doc']) && $this->docs->idExists($_REQUEST['doc'])) {
            return $_REQUEST['doc'];
        }

        return $this->docs->getFirstFile()->getID();
    }

    public function getActiveFile() : DocFile
    {
        return $this->docs->getByID($this->getActiveFileID());
    }

    public function display() : void
    {
        $parser = new DocParser($this->getActiveFile());

?><!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->title ?></title>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#"><?php echo $this->title ?></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Documentation files
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <?php
                                $files = $this->docs->getFiles();

                                foreach ($files as $file) {
                                    ?>
                                        <a class="dropdown-item" href="?doc=<?php echo $file->getID() ?>">
                                            <?php echo $file->getTitle() ?>
                                        </a>
                                    <?php
                                }
                            ?>
                        </div>
                    </li>
                </ul>
            </div>
        </nav>
        <table>
            <tbody>
                <tr>
                    <td id="sidebar">
                        <?php echo $this->renderMenu($parser->getHeaders()); ?>
                    </td>
                    <td id="content">
                        <div class="content-wrapper">
                            <?php echo $parser->render(); ?>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <link rel="stylesheet" href="<?php echo $this->vendorURL ?>/twbs/bootstrap/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="<?php echo $this->vendorURL ?>/mistralys/markdown-viewer/css/styles.css">
        <script src="<?php echo $this->vendorURL ?>/components/jquery/jquery.js"></script>
        <script src="<?php echo $this->vendorURL ?>/twbs/bootstrap/dist/js/bootstrap.js"></script>
    </body>
</html><?php
    }

    /**
     * @param DocHeader[] $headers
     * @return string
     */
    private function renderMenu(array $headers) : string
    {
        ob_start();

        ?>
        <ul class="nav-level-0">
            <?php
            foreach ($headers as $header)
            {
                echo $header->render();
            }
            ?>
        </ul>
        <?php

        return ob_get_clean();
    }
}
