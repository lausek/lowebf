<?php

namespace Umpfertal\Extension;

use Twig\TwigFilter;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Formatter\Crunched;

final class LinkFormat {

    public static function new($env): TwigFilter
    {
        $instance = new self;
        $use_fancy = true; // env->isDebug()
        return new TwigFilter('link_format',
            function ($name, $args=[]) use ($use_fancy)
            {
                switch ($name) {
                case 'post':
                    preg_match('/(\d{4})-(\d{2})-(\d{2})-(.*)/', $args['name'], $groups);
                    echo $use_fancy
                        ? "/post.php?name=${args['name']}"
                        : "/post/${groups[1]}/${groups[2]}/${groups[3]}/${groups[4]}";
                    break;

                case 'team':
                    echo isset($args['team'])
                        ? ($use_fancy
                            ? "/team.php?team=${args['team']}"
                            : "/team/${args['team']}")
                        : ($use_fancy
                            ? "/team.php"
                            : "/team");
                    break;

                case 'gallery':
                    echo isset($args['f'])
                        ? ($use_fancy
                            ? "/gallery.php?f=${args['f']}"
                            : "/gallery/${args['f']}")
                        : ($use_fancy
                            ? "/gallery.php"
                            : "/gallery");
                    break;

                case 'news':
                    echo $use_fancy
                        ? "/news.php?p=${args['p']}"
                        : "/news/${args['p']}";
                    break;

                case 'static':
                    echo $use_fancy
                        ? "/static.php?t=${args['t']}"
                        : "/static/${args['t']}";
                    break;

                case 'feed':
                    echo $use_fancy
                        ? "/feed.php"
                        : "/feed";
                    break;

                case 'download':
                    // fallthrough
                case 'verein':
                    echo $use_fancy
                        ? "/$name.php"
                        : "/$name";
                    break;

                default:
                    echo $name;
                }
            }
        );
    }

}
