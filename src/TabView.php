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

        private function getTabRendered($index): string
        {
            $data = $this->tabs[$index];
            $checked = $data["checked"] ? " checked" : "";

            //change to better value
            $nonce = rand(0, 100000);

            $masterData = [];

/*                 foreach ($this->ref->modals as $data) {
                    $masterData = array_merge($masterData, $data->templateData);
                } */
                
                // $data = $twig->render($this->modalFilename . ".html", $this->masterData);

            return join("", [
                '<div class="w-tab">',
                '<input type="radio" name="tab" data-tabid="'.$index.'" data-modal-ignore id="tab' . $index . '-' . $nonce . '"' . $checked . '>',
                '<label for="tab' . $index . '-' . $nonce . '">' . $data["tabtitle"] . '</label>',
                '<div class="tab-content">',
                // "Hallo Welt 2",
                $this->readTemplate($data["templatename"] . ".html")->render($this->ref->templateData),
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

        public function render(): string
        {            
            return join("", [
                '<div class="tab-view">',
                $this->getAllTabsRendered(),
                '</div>'
            ]);
        }
    }
}
