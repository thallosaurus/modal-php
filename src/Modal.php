<?php
namespace Donstrange\Modalsupport {

    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;

    const SHOW_SUBMIT = 0b001;
    const SHOW_CLOSE = 0b010;
    const SHOW_CLOSE_X = 0b100;

    class Modal {

        
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

        private string $modalFilename;

        private $data = [];

        /**
         * Given content of the modal
         *
         * @var string
         */
        private ?string $content;

        /**
         * Path where all modal artifacts should be loaded from
         */
        private static string $modalArtifactsPath = __DIR__ . "/../example";

        /* TWIG */

        private static $fsLoader = null;
        private static $twig = null;
        private $templateData = [];

        /* TWIG END */

        private string $title = "Micromodal";

        private int $visibleFlags = SHOW_SUBMIT | SHOW_CLOSE | SHOW_CLOSE_X;

        // private string $modalArtifactName;
        
        /**
         * Constructor of the class
         *
         * @param string $id The id of the modal
         * @param string $content Modal content, must be form elements without form parent element
         */
        function __construct(string $id, ?string $content = null) {
            $this->modalId = $id;
            $this->content = $content;

            //default
            $this->modalFilename = $this->modalId;

            //TWIG
            if (self::$fsLoader == null) {
                self::$fsLoader = new FilesystemLoader(self::$modalArtifactsPath);
                self::$twig = new Environment(self::$fsLoader);
            }

            // $this->modalArtifactName = $filename;
            self::$modals[] = $this;
        }
        
        /**
         * Returns a button to open the modal without object support
         *
         * @param string $label
         * @param array $classList
         * @return string
         */
        public function getOpenButton(string $label, $classList = []): string {
            $classstring = join(" ", $classList);
            
            return "<button type='button' class='" . $classstring . "' data-micromodal-trigger='". $this->modalId ."'>" . $label . "</button>";
        }
        
        /**
         * Loads all dependent files as script/style tag. Use this in <head>
         *
         * @return string
         */
        public static function getAssets(): string {
            $jsData = file_get_contents(__DIR__ . "/../assets/micromodal.js");
            $cssData = file_get_contents(__DIR__ . "/../assets/micromodal.css");
            $init = file_get_contents(__DIR__ . "/../assets/init.js");
            return "<style>" . $cssData . "</style>" . "<script>" . $jsData . $init . "</script>";
        }

        public static function getAllModals(): string {
            $map = array_map(function ($m) {
                return $m->getModalContent();
            }, self::$modals);

            return join("", $map);
        }

        public static function setModalPath($path) {
            self::$modalArtifactsPath = $path;
            self::$fsLoader = new FilesystemLoader($path);
            self::$twig = new Environment(self::$fsLoader);
        }

        public function setFilename(string $fname) {
            $this->modalFilename = $fname;
        }

        public function setData(array $data) {
            $this->templateData = $data;
        }

        public function setTitle(string $title) {
            $this->title = $title;
        }

        public function setVisibleFlags($flags) {
            $this->visibleFlags = $flags;
        }
        
        /**
         * Returns the HTML for the whole modal
         *
         * @return string
         */
        public function getModalContent(): string {
            $content = "";
            if (is_null($this->content)) {

                // $content = file_get_contents(self::$modalArtifactsPath . "/" . $this->modalFilename . ".html");

                $content = self::$twig->render($this->modalFilename . ".html", $this->templateData);
            } else {
                $content = $this->content;
            }

            $modalRaw = [
                '<div class="modal micromodal-slide" id="' . $this->modalId . '" aria-hidden="true">',
                '<div class="modal__overlay" tabindex="-1" data-micromodal-close>',
                '<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="' . $this->modalId . '-title">',
                '<form action="#">',
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
                (($this->visibleFlags & SHOW_SUBMIT) == SHOW_SUBMIT) ? '<input class="modal__btn modal__btn-primary" data-ok type="submit">' : '',
                (($this->visibleFlags & SHOW_CLOSE) == SHOW_CLOSE) ? '<button class="modal__btn" data-micromodal-close data-cancel data-modal-ignore aria-label="Close this dialog window">Close</button>' : '',
                '</footer>',
                '</form>',
                '</div>',
                '</div>',
                '</div>'
            ];

            return join("", $modalRaw);
        }
    }
}
?>