<?php

namespace Donstrange\Modalsupport {

    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;

    abstract class TemplateLoader
    {
        /**
         * Path where all modal artifacts should be loaded from
         */
        protected static string $modalArtifactsPath = __DIR__ . "/../modals";

        /* TWIG */

        private static $fsLoader = null;
        private static $twig = null;

        /* TWIG END */

        function __construct()
        {
            //TWIG
            if (self::$fsLoader == null) {
                self::$fsLoader = new FilesystemLoader(self::$modalArtifactsPath);
                self::$twig = new Environment(self::$fsLoader);
            }
        }

        public static function setModalPath($path)
        {
            self::$modalArtifactsPath = $path;
        }

        static $templateCache = [];

        public function readTemplate($mfilename): \Twig\TemplateWrapper {
            // return self::$twig->render($mfilename . ".html", $data);
/*             $path = self::$modalArtifactsPath . "/" . $mfilename . ".html";
            if (isset(self::$templateCache[$path])) {
                return self::$templateCache[$path];
                
            } else {
                $file = file_get_contents($path);
                
                self::$templateCache[$path] = $file;
                return self::$templateCache[$path];
            } */

            return self::$twig->load($mfilename);
        }

        public abstract function render(): string;

    }
}
