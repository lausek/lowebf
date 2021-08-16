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
            new TwigFunction("stylesheet", [$this, "writeStylesheet"])
        ];
    }

    public function writeStylesheet(string $sheet)
    {
        $fileExtension = pathinfo($sheet, PATHINFO_EXTENSION);
        $fileExtension = strtolower($fileExtension);

        //$fileOutputPath = "/resources/css/compiled.css";

        switch ($fileExtension) {
            case "scss":
                $fileInputPath = $this->env->asAbsolutePath("site/css/$sheet");
                $fileInputContent = $this->env->loadFile($fileInputPath);

                $compiledCss = $this->scssCompiler->compile($fileInputContent);

                break;

            default:
                break;
        }

        $href = "";
        //$href = $this->env->cache();

        $html = "<link rel='stylesheet' type='text/css' href='$href'/>";
        $this->env->runtime()->writeOutput($html);
    }
}
