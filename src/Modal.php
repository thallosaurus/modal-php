<?php
namespace Donstrange\Modalsupport {
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

        /**
         * Given content of the modal
         *
         * @var string
         */
        private string $content;
        
        /**
         * Constructor of the class
         *
         * @param string $id The id of the modal
         * @param string $content Modal content, must be form elements without form parent element
         */
        function __construct(string $id, string $content) {
            $this->modalId = $id;
            $this->content = $content;
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
        
        /**
         * Returns the HTML for the whole modal
         *
         * @return string
         */
        public function getModalContent(): string {
            //$content = file_get_contents(__DIR__ . "/../assets/" . $this->modalId . ".html");

            $modalRaw = [
                '<div class="modal micromodal-slide" id="' . $this->modalId . '" aria-hidden="true">',
                '<div class="modal__overlay" tabindex="-1" data-micromodal-close>',
                '<div class="modal__container" role="dialog" aria-modal="true" aria-labelledby="' . $this->modalId . '-title">',
                '<form action="#">',
                '<header class="modal__header">',
                '<h2 class="modal__title" id="' . $this->modalId . '-title">',
                'Micromodal',
                '</h2>',
                '<button class="modal__close" aria-label="Close modal" data-micromodal-close></button>',
                '</header>',
                '<main class="modal__content" id="' . $this->modalId . '-content">',
                $this->content,
                '</main>',
                '<footer class="modal__footer">',
                '<input class="modal__btn modal__btn-primary" id="submit" data-ok type="submit">',
                '<button class="modal__btn" data-micromodal-close data-cancel aria-label="Close this dialog window">Close</button>',
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