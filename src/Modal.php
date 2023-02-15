<?php

namespace Donstrange\Modalsupport {

    use Twig\Environment;
    use Twig\Loader\ArrayLoader;
    use Twig\Loader\FilesystemLoader;

    const SHOW_SUBMIT = 0b001;
    const SHOW_CLOSE = 0b010;
    const SHOW_CLOSE_X = 0b100;

    // const DEFAULT_DIALOG_TEMPLATE = "moin";

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

        /**
         * The filename of the template.
         * Can be null if a tab view is used
         * @var string|null
         */
        private ?string $modalFilename;

        //private $data = [];

        private string $baseFilepath;

        private bool $usesCustomPath = false;

        /**
         * Given content of the modal
         * @var string
         */
        //private $content;

        /**
         * Summary of templateData
         * @var mixed
         */
        public $templateData = [];

        private string $title = "Micromodal";

        private int $visibleFlags = SHOW_SUBMIT | SHOW_CLOSE | SHOW_CLOSE_X;

        private bool $hasTabs = false;
        private TabView $tabView;

        private string $closeLabel = "Schließen";
        private string $submitLabel = "Absenden";

        // private string $modalArtifactName;

        /**
         * Constructs a new modal. Gets added to the global modal queue and gets flushed by calling Modal#getAllModals().
         * @param string $id The unique id of the modal window
         * @param string|null $filename filename of the template that gets used. Can be omitted if using a TabView.
         */
        function __construct(string $id, ?string $filename = null)
        {
            // parent::__construct();
            $this->modalId = $id;
            $this->modalFilename = $filename;
            $this->baseFilepath = __DIR__ . "/../base/base.html";
            self::$modals[] = $this;
        }

        /**
         * Returns a button to open the modal without object support
         *
         * @param string $label Label of the button
         * @param array $classList List of CSS-classes without dot to be applied to the button
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

        /**
         * Summary of getAllModals
         * @param mixed $debug Debug Flag (unused as of 0.0.1)
         * @return string
         */
        public static function getAllModals($debug = false)
        {
            $map = array_map(function ($m) {
                return $m->render();
            }, self::$modals);
            return join("", $map);
        }

        /**
         * Set the filename of the used template
         * @param string $fname The Filename with extension
         * @return void
         */
        public function setFilename(string $fname)
        {
            $this->modalFilename = $fname;
        }

        /**
         * Sets the Data to be filled in the templates
         * @param array $data Key/Value Array of the data
         * @return void
         */
        public function setData(array $data)
        {
            $this->templateData = $data;
        }

        /**
         * Sets the master title of the modal
         * @param string $title The title to be used
         * @return void
         */
        public function setTitle(string $title)
        {
            $this->title = $title;
        }

        /**
         * Set flags that control which buttons get shown at the bottom.
         * Also controls the small X in the right corner
         * @param mixed $flags See Modal#SHOW_SUBMIT, Modal#SHOW_CLOSE, Modal#SHOW_CLOSE_X
         * @return void
         */
        public function setVisibleFlags($flags)
        {
            $this->visibleFlags = $flags;
        }

        /**
         * Sets the Path to the template that should be used as base for this dialog
         * @param string $tmplPath The path to the template base
         */
        public function setBaseTemplatePath($tmplPath) {
            $this->baseFilepath = $tmplPath;
            $this->usesCustomPath = true;
        }

        /**
         * Sets and activates a tab view for the modal
         * @param TabView $view The Tabview to be used
         * @return void
         */
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
            $loader = new FilesystemLoader([
                dirname($this->baseFilepath)
            ]);
            
            $twig = new Environment($loader);

            return $twig->render(basename($this->baseFilepath), [
                "modalId" => $this->modalId,
                "hasTabs" => $this->hasTabs,
                "modalTitle" => $this->title,
                "showCloseX" => ($this->visibleFlags & SHOW_CLOSE_X) == SHOW_CLOSE_X,
                "showSubmit" => (($this->visibleFlags & SHOW_SUBMIT) == SHOW_SUBMIT),
                "showClose" => (($this->visibleFlags & SHOW_CLOSE) == SHOW_CLOSE),
                "submitLabel" => $this->submitLabel,
                "closeLabel" => $this->closeLabel,
                "content" => $content
            ]);
        }

        /**
         * Returns the rendered Modal as HTML
         * @return string
         */
        public function render(): string
        {
            if ($this->hasTabs) {
                $data = $this->tabView->render();
            } else {
                $loader = new FilesystemLoader([
                    self::$modalArtifactsPath
                ]);
                
                $twig = new Environment($loader);

                $masterData = [];

                foreach (self::$modals as $data) {
                    $masterData = array_merge($masterData, $data->templateData);
                }
                
                $data = $twig->render($this->modalFilename . ".html", $this->templateData);
            }
            return $this->getModalContent($data);
        }
    }
}
