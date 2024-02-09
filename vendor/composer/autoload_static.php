<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInita22c1d29ffec20cbc71f23dc0cacfb42
{
    public static $prefixLengthsPsr4 = array (
        'A' => 
        array (
            'Arostech\\ApiAndApiController\\' => 29,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Arostech\\ApiAndApiController\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInita22c1d29ffec20cbc71f23dc0cacfb42::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInita22c1d29ffec20cbc71f23dc0cacfb42::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInita22c1d29ffec20cbc71f23dc0cacfb42::$classMap;

        }, null, ClassLoader::class);
    }
}
