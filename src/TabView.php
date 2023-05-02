<?php

namespace Donstrange\Modalsupport {
    
    class TabView extends TemplateLoader
    {
        /**
         * Holds all added templates for each tab
         * @var array
         */
        private array $tabs = [];



        /**
         * Adds a template to the TabView
         * @param mixed $title The Label of the Tab
         * @param mixed $templatename The Filename of the template without extension (.html)
         * @param mixed $checked Is true if this tab should be preselected
         * @return void
         */
        public function addTemplate($title, $templatename, $checked = false)
        {
            $this->tabs[] = [
                "tabtitle" => $title,
                "templatename" => $templatename,
                "checked" => $checked
            ];
        }

        /**
         * Renders the Tab of the given Index and returns it as string
         * @param mixed $index Tab Index
         * @return string
         */
        private function getTabRendered($index): string
        {
            $data = $this->tabs[$index];
            $checked = $data["checked"] ? " checked" : "";

            //change to better value
            $nonce = rand(0, 100000);

            // $masterData = [];

            return join("", [
                '<div class="w-tab">',
                '<input type="radio" name="tab" data-tabid="'.$index.'" data-modal-ignore id="tab' . $index . '-' . $nonce . '"' . $checked . '>',
                '<label for="tab' . $index . '-' . $nonce . '">' . $data["tabtitle"] . '</label>',
                '<div class="tab-content">',
                $this->readTemplate($data["templatename"] . ".html")->render($this->ref->templateData),
                '</div>',
                '</div>',
            ]);
        }

        /**
         * Renders all Tabs as HTML
         * @return string
         */
        private function getAllTabsRendered(): string {
            $str = "";

            for ($i = 0; $i < sizeof($this->tabs); $i++) {
                $str .= $this->getTabRendered($i);
            }

            return $str;
        }

        /**
         * Renders the whole TabView
         * @return string
         */
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
