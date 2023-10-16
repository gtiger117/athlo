<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit3c63b48408db7eea1039ea96a6040b80
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'Mypackage\\Athlo\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Mypackage\\Athlo\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit3c63b48408db7eea1039ea96a6040b80::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit3c63b48408db7eea1039ea96a6040b80::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit3c63b48408db7eea1039ea96a6040b80::$classMap;

        }, null, ClassLoader::class);
    }
}