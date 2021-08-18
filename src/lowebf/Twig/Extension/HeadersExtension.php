<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

use ScssPhp\ScssPhp\Compiler;
use ScssPhp\ScssPhp\Formatter\Crunched;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class HeadersExtension extends AbstractExtension
{
    /** @var int */
    const MAX_TITLE_LENGTH = 35;

    /** @var int */
    const MAX_DESCRIPTION_LENGTH = 65;

    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFilters()
    {
        return [
            new TwigFilter("limitLength", [$this, "getTrimmedString"])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("linkPreviewHeaders", [$this, "writeLinkPreviewHeaders"])
        ];
    }

    public function getTrimmedString(string $full, int $maxLen)
    {
        if ($maxLen < strlen($full)) {
            return substr($full, 0, $maxLen - 1) . '…';
        }

        return $full;
    }

    public function writeLinkPreviewHeaders(array $args)
    {
        $defaultOptions = [
            "previewImageUrl" => "",
            "url" => "",
            "title" => "",
            "description" => "",
            "type" => "article",
            "locale" => "en_US",
        ];
        $options = array_merge($defaultOptions, $args);

        $options["title"] = $this->getTrimmedString($options["title"], self::MAX_TITLE_LENGTH);
        $options["description"] = $this->getTrimmedString($options["description"], self::MAX_DESCRIPTION_LENGTH);

        $options = (object)$options;

        $headers = "";
        $headers .= "<meta property='og:image' content='$options->previewImageUrl'>";
        $headers .= "<meta property='og:title' content='$options->title'>";
        $headers .= "<meta property='og:url' content='$options->url'>";
        $headers .= "<meta property='og:description' content='$options->description'>";
        $headers .= "<meta property='og:type' content='$options->type'>";
        $headers .= "<meta property='og:locale' content='$options->locale'>";

        $this->env->runtime()->writeOutput($headers);
    }
}
