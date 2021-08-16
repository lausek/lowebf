<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class StylesheetExtension extends AbstractExtension
{
    /** @var Compiler|null */
    private $scssCompiler = null;

    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;

        $this->scssCompiler = new Compiler();
        $this->scssCompiler->setFormatter(new Crunched());
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("stylesheet", [$this, "writeStylesheetLink"])
        ];
    }

    public function writeStylesheetLink(string $sheet)
    {
        //$fileOutputPath = "/resources/css/compiled.css";
        $href = "";

        // TODO: check if cache needs rebuild
        if (true) {
            $fileInputPath = $this->env->asAbsolutePath("site/css/$sheet");
            $fileInputContent = $this->env->loadFile($fileInputPath);

            $compiledCss = $this->scssCompiler->compile($fileInputContent);
        }

        /*

        $cssHandle = fopen($_SERVER['DOCUMENT_ROOT'] . $filePath, 'w');
        if ($cssHandle !== NULL)
        {
            foreach ($sheets as $stylesheet)
            {
                fwrite($cssHandle, ); 
            }
            fclose($cssHandle);
        }

        echo "";
         */

        $html = "<link rel='stylesheet' type='text/css' href='$href'/>";
        $this->env->runtime()->writeOutput($html);
    }
}
