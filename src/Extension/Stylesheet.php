<?php

declare(strict_types=1);

namespace lowebf\Extension;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;
use lowebf\Environment;
use Twig\TwigFunction;

final class Stylesheet
{
    private static $scss = null;

    public static function new(): TwigFunction
    {
        if (null === self::$scss) {
            self::$scss = new Compiler();
            self::$scss->setFormatter(new Crunched());
        }

        return new TwigFunction(
            'stylesheet',
            function ($stylesheets): void {
                $env = Environment::getInstance();

                $out_path = '/cache/css/compiled.css';
                $out_url = $env->asAbsoluteUrl('/cache/css/compiled.css');
                $out_handle = fopen($env->asAbsolutePath($out_path), 'w');
                if (null !== $out_handle) {
                    foreach ($stylesheets as $sheet) {
                        $css_content_path = $env->asAbsolutePath($sheet);
                        $css_content = file_get_contents($css_content_path);
                        fwrite($out_handle, self::$scss->compile($css_content));
                    }
                    fclose($out_handle);
                }

                echo "<link rel='stylesheet' type='text/css' href='${out_url}'/>";
            }
        );
    }
}
