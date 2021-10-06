<?php

namespace lowebf\Twig\Extension;

use lowebf\Environment;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigTest;

final class HelperExtension extends AbstractExtension
{
    /** @var Environment */
    private $env = null;

    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFilters()
    {
        return [
            new TwigFilter("base64encode", [$this, "getBase64EncodedString"]),
            new TwigFilter("base64decode", [$this, "getBase64DecodedString"]),
            new TwigFilter("limitLength", [$this, "getTrimmedString"])
        ];
    }

    public function getTests()
    {
        return [
            new TwigTest(
                "array",
                function($obj) {
                    return is_array($obj);
                }
            ),
        ];
    }

    public function getTrimmedString(string $full, int $maxLen)
    {
        if ($maxLen < strlen($full)) {
            return substr($full, 0, $maxLen - 1) . '…';
        }

        return $full;
    }

    public function getBase64EncodedString(string $input)
    {
        return base64_encode($input);
    }

    public function getBase64DecodedString(string $input)
    {
        return base64_decode($input);
    }
}
