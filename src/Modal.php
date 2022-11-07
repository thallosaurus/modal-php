<?php
namespace Donstrange\Modalsupport {
    class Modal {
        
        private static $modals = [];
        
        private string $modalId;
        
        function __construct(string $id) {
            $this->modalId = $id;
            self::$modals[] = $this;
        }
        
        public function getOpenButton(string $label, $classList = []): string {
            $classstring = join(" ", $classList);
            
            return "<button type='button' class='" . $classstring . "' data-micromodal-trigger='". $this->modalId ."'>" . $label . "</button>";
        }
        
        public static function getAssets(): string {
            $jsData = file_get_contents(__DIR__ . "/../assets/micromodal.js");
            $cssData = file_get_contents(__DIR__ . "/../assets/micromodal.css");
            $init = file_get_contents(__DIR__ . "/../assets/init.js");
            return "<style>" . $cssData . "</style>" . "<script>" . $jsData . $init . "</script>";
        }
        
        public function getModalContent(): string {
            $content = file_get_contents(__DIR__ . "/../assets/" . $this->modalId . ".html");
            return $content;
        }
    }
}
?>