<?php

namespace lowebf\Parser;

use function lowebf\getFileType;
use lowebf\Environment;
use lowebf\Error\FileNotFoundException;

class Markdown extends \Michelf\Markdown {
    /** @var Environment */
    protected $env;

    public function __construct(Environment $env)
    {
        parent::__construct();

        $this->env = $env;
        $this->url_filter_func = function($url) {
            try {
                return $this->env->route()->urlFor($url);
            } catch (FileNotFoundException $e) {
                return $url;
            }
        };
    }

    /**
	 * Callback to parse inline image tags
	 * @param  array $matches
	 * @return string
	 */
	    protected function _doImages_inline_callback($matches)
    {
        $wholeMatch = $matches[1];
        $altText = $matches[2];
        $url = $matches[3] == '' ? $matches[4] : $matches[3];
        $title = &$matches[7];

        $fileType = getFileType($url);

        switch ($fileType) {
            case "mp4":
                $result = $this->createVideoElement($altText, $url, $title, $fileType);
                break;

            default:
                $result = $this->createImageElement($altText, $url, $title);
                break;
        }

        return $this->hashPart($result);
    }

    protected function createImageElement($altText, $url, $title) : string
    {
        $altText = $this->encodeAttribute($altText);
        $url = $this->encodeURLAttribute($url);
        $result = "<img src=\"$url\" alt=\"$altText\"";
        if (isset($title)) {
            $title = $this->encodeAttribute($title);
            $result .= " title=\"$title\""; // $title already quoted
        }
        $result .= $this->empty_element_suffix;

        return $result;
    }

    protected function createVideoElement($altText, $url, $title, string $extension) : string
    {
        $altText = $this->encodeAttribute($altText);
        $url = $this->encodeURLAttribute($url);

        $result = "<center><video controls>";
        $result .= "<source src=\"$url\" type=\"video/$extension\">";
        $result .= "Your browser does not support the video tag.</video></center>";

        return $result;
    }
}
