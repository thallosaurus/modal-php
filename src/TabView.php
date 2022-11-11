<?php

namespace Donstrange\Modalsupport {
    class TabView extends TemplateLoader
    {
        private array $tabs = [];
        private ?Modal $ref = null;

        public function addTemplate($title, $templatename, $checked = false)
        {
            $this->tabs[] = [
                "tabtitle" => $title,
                "templatename" => $templatename,
                "checked" => $checked
            ];
        }

        private function getTabRendered($index, $data): string
        {
            $data = $this->tabs[$index];
            $checked = $data["checked"] ? " checked" : "";

            return join("", [
                '<div class="w-tab">',
                '<input type="radio" name="tab" data-modal-ignore id="tab' . $index . '"' . $checked . '>',
                '<label for="tab'. $index . '">' . $data["tabtitle"] . '</label>',
                '<div class="tab-content">',
                // Hallo Welt 2
                $this->readTemplate($data["templatename"], $data),
                // $data["content"],
                '</div>',
                '</div>',
            ]);
        }

        public function setRef(Modal $modal) {
            // $this->ref = $modal;
        }

        private function getAllTabsRendered($data): string {
            $str = "";

            for ($i = 0; $i < sizeof($this->tabs); $i++) {
                $str .= $this->getTabRendered($i, $data);
            }

            return $str;
        }

        public function render(array $data)
        {            
            return join("", [
                '<div class="tab-view">',
                $this->getAllTabsRendered($data),
                '</div>'
            ]);
        }
    }
}
?>