<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit08d665bea3f74c6cfa2c723613dbbeeb
{
    public static $files = array (
        '2cffec82183ee1cea088009cef9a6fc3' => __DIR__ . '/..' . '/ezyang/htmlpurifier/library/HTMLPurifier.composer.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'H' => 
        array (
            'HTMLPurifier' => 
            array (
                0 => __DIR__ . '/..' . '/ezyang/htmlpurifier/library',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit08d665bea3f74c6cfa2c723613dbbeeb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit08d665bea3f74c6cfa2c723613dbbeeb::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit08d665bea3f74c6cfa2c723613dbbeeb::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
