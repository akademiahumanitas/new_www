<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit461b4f7f917a59701cbbbecc9b33d344
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit461b4f7f917a59701cbbbecc9b33d344', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit461b4f7f917a59701cbbbecc9b33d344', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit461b4f7f917a59701cbbbecc9b33d344::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
