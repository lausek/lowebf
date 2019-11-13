<?php

namespace lowebf\Extension;

use Twig\TwigFunction;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

final class Stylesheet {

    private static $scss = NULL;
    private $cache = NULL;

    public static function new($cache): TwigFunction
    {
        $instance = new self($cache);
        return new TwigFunction('stylesheet',
            function ($sheets) use ($instance)
            {
                $filePath = '/resources/css/compiled.css';

                $cssHandle = fopen($_SERVER['DOCUMENT_ROOT'] . $filePath, 'w');
                if ($cssHandle !== NULL)
                {
                    foreach ($sheets as $stylesheet)
                    {
                        fwrite($cssHandle, self::$scss->compile(file_get_contents("${_SERVER['DOCUMENT_ROOT']}/$stylesheet")));
                    }
                    fclose($cssHandle);
                }

                echo "<link rel='stylesheet' type='text/css' href='$filePath'/>";
            }
        );
    }

    public function __construct($cache)
    {
        if (self::$scss === NULL)
        {
            self::$scss = new Compiler();
            self::$scss->setFormatter(new Crunched());
        }
        $this->cache = $cache;
    }

}
