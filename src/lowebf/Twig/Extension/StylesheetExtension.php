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
        $fileInputPath = $this->env->asAbsolutePath("site/css/$sheet");
        $fileExtension = pathinfo($fileInputPath, PATHINFO_EXTENSION);
        $fileExtension = strtolower($fileExtension);

        switch ($fileExtension) {
            case "scss":
                $fileName = pathinfo($fileInputPath, PATHINFO_FILENAME);
                $lastModified = $this->env->getLastModified($fileInputPath);
                $compiledCssPath = "css/$fileName-$lastModified.css";

                if (!$this->env->cache()->exists($compiledCssPath)) {
                    $fileInputContent = $this->env->loadFile($fileInputPath)->unwrap();

                    $compiledCss = $this->scssCompiler->compile($fileInputContent);

                    $this->env->cache()->set($compiledCssPath, $compiledCss);
                }

                $cssRelativePath = "/cache/$compiledCssPath";
                break;

            default:
                $cssRelativePath = "/css/$sheet";
                break;
        }

        $href = $this->env->route()->urlFor($cssRelativePath);
        $html = "<link rel='stylesheet' type='text/css' href='$href'/>";
        $this->env->runtime()->writeOutput($html);
    }
}
