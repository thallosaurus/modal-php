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

        /**
         * Twig FilesystemLoader
         * @var mixed
         */
        private static $fsLoader = null;

        /**
         * Twig Engine
         * @var mixed
         */
        private static $twig = null;

        /* TWIG END */

        /**
         * Only if it is not initialized yet it should assign a new Filesystem Loader
         */
        function __construct()
        {
            //TWIG
            if (self::$fsLoader == null) {
                self::$fsLoader = new FilesystemLoader(self::$modalArtifactsPath);
                self::$twig = new Environment(self::$fsLoader);
            }
        }

        /**
         * Sets the basepath where the loader should search for templates
         * @param mixed $path
         * @return void
         */
        public static function setModalPath($path)
        {
            self::$modalArtifactsPath = $path;
        }

        /**
         * @deprecated v0.0.2
         * @var mixed
         */
        static $templateCache = [];

        /**
         * Loads a template and returns the twig instance
         * @param mixed $mfilename Filename of the template (without extension)
         * @return \Twig\TemplateWrapper
         */
        public function readTemplate($mfilename): \Twig\TemplateWrapper {
            return self::$twig->load($mfilename);
        }

        /**
         * Interface Method for rendering
         * @return string
         */
        public abstract function render(): string;
    }
}
