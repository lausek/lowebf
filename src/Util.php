<?php

declare(strict_types=1);

namespace lowebf;

final class Util
{
    public static function read_dir($dir): array
    {
        return array_filter(
            scandir($dir),
            function ($name) {
                return 0 !== strpos($name, '.');
            }
        );
    }

    public static function load_file(string $path): array
    {
        $ext = pathinfo($path, PATHINFO_EXTENSION);
        switch ($ext) {
            case 'json':
                return self::load_json_file($path);
            case 'md':
                return self::load_md_file($path);
            default:
                return null;
        }
    }

    public static function load_json_file(string $path): array
    {
        $content = file_get_contents($path);
        if (false === $content) {
            return [];
        }
        $json = json_decode($content);

        return null !== $json ? (array) $json : [];
    }

    public static function load_md_file(string $path): array
    {
        $matches = [];
        $content = file_get_contents($path);

        if (! preg_match('/---\n(.*)\n---\n([\s\S]*)/m', $content, $matches)) {
            return [];
        }

        $parsed = Spyc::YAMLLoadString($matches[1]);
        //$parsed = Spyc::YAMLLoadString($matches[1]);

        $text_content = $matches[2];

        $parsed['name'] = pathinfo($path, PATHINFO_FILENAME);
        $parsed['date'] = substr($path, 0, 10);

        if (isset($parsed['gallery'])) {
            $galleryPath = "/data/img/gallery/${parsed['gallery']}";
            $images = array_filter(
                scandir(Config::asAbsolute("${galleryPath}/full/")),
                function ($name) {
                    return 0 !== strpos($name, '.');
                }
            );

            foreach ($images as $image) {
                self::generateThumbnail("${galleryPath}/full/${image}", [200, 200], "${galleryPath}/thumbs/");
            }

            $parsed['gallery'] = [
                'path' => $galleryPath,
                'images' => $images,
            ];
        }

        $parsed['content'] = Markdown::defaultTransform($rawContent);

        $parsed['short'] = strip_tags($parsed['content']);
        $parsed['short'] = substr($parsed['short'], 0, Config::NEWS_SHORT_LENGTH - 3) . '...';

        if (! array_key_exists('preview', $parsed) && isset($parsed['bild'])) {
            $parsed['preview'] = pathinfo($parsed['bild'], PATHINFO_BASENAME);
            // TODO: check if thumbnail already exists
            self::generateThumbnail($parsed['bild'], [200, 200], '/cache/thumbs/medium');
            self::generateThumbnail($parsed['bild'], [200, 200], '/cache/thumbs/small');
        }

        return $parsed;

        return null !== $json ? (array) $json : [];
    }
}
