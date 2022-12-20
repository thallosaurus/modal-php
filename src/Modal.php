<?php

namespace Donstrange\Modalsupport {

    use Twig\Environment;
    use Twig\Loader\ArrayLoader;
    use Twig\Loader\FilesystemLoader;

    const SHOW_SUBMIT = 0b001;
    const SHOW_CLOSE = 0b010;
    const SHOW_CLOSE_X = 0b100;

    class Modal extends TemplateLoader
    {


        /**
         * Holds all declared modals
         *
         * @var array
         */
        private static $modals = [];

        /**
         * The ID of the modal
         *
         * @var string
         */
        private string $modalId;

        private ?string $modalFilename;

        private $data = [];

        /**
         * Given content of the modal
         *
         * @var string
         */
        private $content;

        public $templateData = [];

        private string $title = "Micromodal";

        private int $visibleFlags = SHOW_SUBMIT | SHOW_CLOSE | SHOW_CLOSE_X;

        private bool $hasTabs = false;
        private TabView $tabView;

        private string $closeLabel = "Schließen";
        private string $submitLabel = "Absenden";

        // private string $modalArtifactName;

        /**
         * Constructor of the class
         *
         * @param string $id The id of the modal
         * @param string $content Modal content, must be form elements without form parent element
         */
        function __construct(string $id, ?string $filename = null)
        {
            // parent::__construct();
            $this->modalId = $id;
            $this->modalFilename = $filename;

            //default
            // $this->modalFilename = $this->modalId;

            // $this->modalArtifactName = $filename;
            self::$modals[] = $this;
        }

        /**
         * Returns a button to open the modal without object support
         *
         * @param string $label
         * @param array $classList
         * @deprecated
         * @return string
         */
        public function getOpenButton(string $label, $classList = []): string
        {
            $classstring = join(" ", $classList);

            return "<button type='button' class='" . $classstring . "' data-micromodal-trigger='" . $this->modalId . "'>" . $label . "</button>";
        }

        public function setCloseLabel(string $label) {
            $this->closeLabel = $label;
        }

        public function setSubmitLabel(string $label) {
            $this->submitLabel = $label;
        }

        /**
         * Loads all dependent files as script/style tag. Use this in <head>
         *
         * @return string
         */
        public static function getAssets(): string
        {
            $jsData = file_get_contents(__DIR__ . "/../assets/micromodal.js");
            $cssData = file_get_contents(__DIR__ . "/../assets/micromodal.css");
            $tabCss = file_get_contents(__DIR__ . "/../assets/tabs.css");
            $init = file_get_contents(__DIR__ . "/../assets/init.js");
            return "<style>" . $cssData . $tabCss . "</style>" . "<script>" . $jsData . $init . "</script>";
        }

        public static function getAllModals($debug = false)
        {
            /*if (!$debug) {
                $map = array_map(function ($m) {
                    return $m->render();
                }, self::$modals);
                return join("", $map);
            } else {

                $map = array_map(function ($m) {
                    return [
                        "template" => $m->render(),
                        "data" => $m->templateData
                    ];
                }, self::$modals);

                //prepare master template here
                $masterTemplate = join("<br>", array_map(
                    function ($e) {
                        return $e["template"];
                    },
                    $map
                ));


                $masterData = [];

                foreach ($map as $data) {
                    $masterData = array_merge($masterData, $data["data"]);
                }
                if ($debug) {
                    var_dump($masterData);
                    var_dump(htmlentities($masterTemplate));
                }


                $loader = new ArrayLoader(["tmp" => $masterTemplate]);
                $twig = new Environment($loader);

                //return $masterTemplate;
                return $twig->render("tmp", $masterData);
                //return $this->getModalContent();
            }

            //return join("", $map);*/

            $map = array_map(function ($m) {
                return $m->render();
            }, self::$modals);
            return join("", $map);
        }

        /**
         * @deprecated
         */
        public function setFilename(string $fname)
        {
            $this->modalFilename = $fname;
        }

        public function setData(array $data)
        {
            $this->templateData = $data;
        }

        public function setTitle(string $title)
        {
            $this->title = $title;
        }

        public function setVisibleFlags($flags)
        {
            $this->visibleFlags = $flags;
        }

        public function addTabView(TabView $view)
        {
            $view->setRef($this);
            $this->tabView = $view;
            $this->hasTabs = true;
        }

        /**
         * Returns the HTML for the whole modal
         *
         * @return string
         */
        public function getModalContent($content): string
        {
/*             $content = "";
            if ($this->hasTabs) {
                $content = $this->tabView->render();
                // print_r($content);
                // $content = file_get_contents(self::$modalArtifactsPath . "/" . $this->modalFilename . ".html");

                // $content = $this->readTemplate($this->modalFilename, $this->templateData);
            } else {
                // var_dump($this->content);
                $content = $this->render();
            } */

            $modalRaw = [
                '<div class="modal micromodal-slide" id="' . $this->modalId . '" aria-hidden="true">',
                '<div class="modal__overlay" tabindex="-1" data-micromodal-close>',
                '<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="' . $this->modalId . '-title">',
                '<form action="#"' . ($this->hasTabs ? " data-has-tabs" : "") . '>',
                '<header class="modal__header">',
                '<h2 class="modal__title" id="' . $this->modalId . '-title">',
                $this->title,
                '</h2>',
                (($this->visibleFlags & SHOW_CLOSE_X) == SHOW_CLOSE_X) ? '<button class="modal__close" aria-label="Close modal" data-modal-ignore data-micromodal-close></button>' : '',
                '</header>',
                '<main class="modal__content" id="' . $this->modalId . '-content">',
                $content,
                '</main>',
                '<footer class="modal__footer">',
                (($this->visibleFlags & SHOW_SUBMIT) == SHOW_SUBMIT) ? '<input class="modal__btn modal__btn-primary" data-ok type="submit" value="'.$this->submitLabel.'">' : '',
                (($this->visibleFlags & SHOW_CLOSE) == SHOW_CLOSE) ? '<button class="modal__btn" data-micromodal-close data-cancel data-modal-ignore aria-label="Close this dialog window">'. $this->closeLabel .'</button>' : '',
                '</footer>',
                '</form>',
                '</div>',
                '</div>',
                '</div>'
            ];

            return join("", $modalRaw);
        }

        public function render(): string
        {
            // $loader = new ArrayLoader(["tmp" => $this->getModalContent()]);
            // $path = self::$modalArtifactsPath;

            if ($this->hasTabs) {
                $data = $this->tabView->render();
                // print_r($content);
                // $content = file_get_contents(self::$modalArtifactsPath . "/" . $this->modalFilename . ".html");

                // $content = $this->readTemplate($this->modalFilename, $this->templateData);
                // var_dump($this->modalFilename);
            } else {
                // var_dump($this->content);
                // $content = $this->render();
                $loader = new FilesystemLoader([
                    self::$modalArtifactsPath
                ]);
                
                $twig = new Environment($loader);
                
                // var_dump($this->modalFilename);

                $masterData = [];

                foreach (self::$modals as $data) {
                    $masterData = array_merge($masterData, $data->templateData);
                }
                
                $data = $twig->render($this->modalFilename . ".html", $this->templateData);
                // return $this->getModalContent($data);
            }
            return $this->getModalContent($data);
            //return $this->getModalContent();
        }
    }
}
