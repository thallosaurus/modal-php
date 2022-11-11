<?php

namespace Donstrange\Modalsupport {
    class TabView
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

        private function getTabRendered($index): string
        {
            $data = $this->tabs[$index];
            $checked = $data["checked"] ? " checked" : "";

            return join("", [
                '<div class="w-tab">',
                '<input type="radio" name="tab" data-modal-ignore id="tab' . $index . '"' . $checked . '>',
                '<label for="tab'. $index . '">' . $data["tabtitle"] . '</label>',
                '<div class="tab-content">',
                // Hallo Welt 2
                $this->ref->readTemplate($data["templatename"]),
                // $data["content"],
                '</div>',
                '</div>',
            ]);
        }

        public function setRef(Modal $modal) {
            $this->ref = $modal;
        }

        private function getAllTabsRendered(): string {
            $str = "";

            for ($i = 0; $i < sizeof($this->tabs); $i++) {
                $str .= $this->getTabRendered($i);
            }

            return $str;
        }

        public function __toString()
        {            
            return join("", [
                '<div class="tab-view">',
                $this->getAllTabsRendered(),
                '</div>'
            ]);
        }
    }
}
?>