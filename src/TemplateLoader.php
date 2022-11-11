<?php

namespace Donstrange\Modalsupport {

    use Twig\Loader\FilesystemLoader;
    use Twig\Environment;

    class TemplateLoader
    {
        /**
         * Path where all modal artifacts should be loaded from
         */
        private static string $modalArtifactsPath = __DIR__ . "/../example";

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
            self::$fsLoader = new FilesystemLoader($path);
            self::$twig = new Environment(self::$fsLoader);
        }

        public function readTemplate($mfilename, $data): string {
            return self::$twig->render($mfilename . ".html", $data);
        }
    }
}
