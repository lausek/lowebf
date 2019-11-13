<?php

namespace lowebf;

use Spyc;
use Michelf\Markdown;

final class News {

    private static $articles = NULL;

    private static function load(): array
    {
        if(self::$articles === NULL)
        {
            $dirPath = Config::getPostDir();
            $articlesRaw = scandir($dirPath, SCANDIR_SORT_DESCENDING);
            self::$articles = [];

            foreach ($articlesRaw as $articleRaw)
            {
                if (substr($articleRaw, 0, 1) === '.')
                {
                    break;
                }
                self::$articles[] = $articleRaw;
            }
        }

        return self::$articles;
    }

    private static function parse(string $article): array
    {
        $filePath = Config::getPostDir()."/$article";
        $fc = file_get_contents($filePath);

        if (!preg_match('/---\n(.*)\n---\n([\s\S]*)/m', $fc, $matches))
        {
            return [];
        }

        $parsed = Spyc::YAMLLoadString($matches[1]);
        $parsed = Spyc::YAMLLoadString($matches[1]);

        $rawContent = $matches[2];

        $parsed['name'] = pathinfo($article, PATHINFO_FILENAME);
        $parsed['date'] = substr($article, 0, 10);

        if (isset($parsed['gallery']))
        {
            $galleryPath = "/data/img/gallery/${parsed['gallery']}";
            $images = array_filter(scandir(Config::asAbsolute("$galleryPath/full/")),
                function ($name)
                {
                    return strpos($name, '.') !== 0;
                }
            );

            foreach($images as $image)
            {
                self::generateThumbnail("$galleryPath/full/$image", [200, 200], "$galleryPath/thumbs/");
            }

            $parsed['gallery'] = [
                'path' => $galleryPath,
                'images' => $images,
            ];
        }

        $parsed['content'] = Markdown::defaultTransform($rawContent);

        $parsed['short'] = strip_tags($parsed['content']);
        $parsed['short'] = substr($parsed['short'], 0, Config::NEWS_SHORT_LENGTH-3) . '...';

        if (!array_key_exists('preview', $parsed) && isset($parsed['bild']))
        {
            $parsed['preview'] = pathinfo($parsed['bild'], PATHINFO_BASENAME);
            // TODO: check if thumbnail already exists
            self::generateThumbnail($parsed['bild'], [200, 200], '/cache/thumbs/medium');
            self::generateThumbnail($parsed['bild'], [200, 200], '/cache/thumbs/small');
        }

        return $parsed;
    }

    private static function generateThumbnail($source, $size, $saveTo)
    {
        $fileName = pathinfo($source, PATHINFO_BASENAME);
        $handle = @fopen(Config::asAbsolute($source), 'r');
        if ($handle !== false)
        {
            $image = new \Imagick();
            $image->readImageFile($handle);

            $swidth = $size[0];
            $sheight = $size[1];

            $imageWidth = $image->getImageWidth();
            $imageHeight = $image->getImageHeight();

            $image->resizeImage($imageWidth*0.5, $imageHeight*0.5, \Imagick::INTERPOLATE_AVERAGE, 1);
            $image->cropImage($swidth*2, $sheight*2, $imageWidth/4-$swidth, $imageHeight/4-$sheight);
            $image->thumbnailImage($swidth, $sheight, true);

            $dirname = Config::asAbsolute($saveTo);
            if (!file_exists($dirname))
            {
                mkdir($dirname, 0777, true);
            }

            $image->writeImage("$dirname/$fileName");
            $image->destroy();

            fclose($handle);
        }
    }

    private static function parseArray(array $articles): array
    {
        return array_map('self::parse', $articles);
    }

    public static function getCount()
    {
        return count(self::load());
    }

    public static function getPostByName(string $name)
    {
        $filePath = Config::getPostDir()."/$name.yaml";
        if (!file_exists($filePath))
        {
            return NULL;
        }
        return self::parse("$name.yaml");
    }

    public static function getPage(int $page): array
    {
        $articles = self::load();

        if (self::getCount() <= Config::NEWS_PAGE_COUNT)
        {
            return self::parseArray($articles);
        }

        $paged = array_chunk($articles, Config::NEWS_PAGE_COUNT);

        if (!array_key_exists($page-1, $paged))
        {
            return [];
        }

        return self::parseArray($paged[$page-1]);
    }

}
