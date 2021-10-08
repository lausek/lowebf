<?php

namespace lowebf\Twig\Extension;

use function lowebf \getFileType;
use lowebf \Environment;

use ScssPhp \ScssPhp \Compiler;
use ScssPhp \ScssPhp \FileReader \FileReaderInterface;
use ScssPhp \ScssPhp \OutputStyle;
use Twig \Extension \AbstractExtension;
use Twig \TwigFunction;

class FileReader implements FileReaderInterface {
    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function isDirectory(string $key) : bool
    {
        throw new \Exception("not implemented");
    }

    public function isFile(string $key) : bool
    {
        return $this->env->filesystem()->exists($key);
    }

    public function getContent(string $key)
    {
        return $this->env->filesystem()->loadFile($key)->unwrap();
    }

    public function getKey(string $key)
    {
        return $key;
    }

    public function getTimestamp(string $key)
    {
        return $this->env->filesystem()->lastModified($key);
    }
}

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
        $this->scssCompiler->setOutputStyle(OutputStyle::COMPRESSED);
        $this->scssCompiler->addImportPath($env->asAbsolutePath("site/css"));

        $this->scssCompiler->setFileReader(new FileReader($env));
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
        $fileType = getFileType($fileInputPath);

        switch ($fileType) {
            case "scss":
                $fileName = pathinfo($fileInputPath, PATHINFO_FILENAME);
                $lastModified = $this->env->getLastModified($fileInputPath);
                $compiledCssPath = "css/$fileName-$lastModified.css";

                if (!$this->env->cache()->exists($compiledCssPath)) {
                    $fileInputContent = $this->env->loadFile($fileInputPath)->unwrap();

                    $compiledCss = $this->scssCompiler->compileString($fileInputContent)->getCss();

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
