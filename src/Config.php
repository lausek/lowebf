<?php

namespace lowebf;

final class Config {

    const SITE_TITLE = '';
    const SITE_URL = '';

    const LINK_FACEBOOK = '';
    const LINK_FUPA = '';
    const LINK_FUSSBALL = '';

    const PATH_POST_DIR = '/data/posts';
    const PATH_DOWNLOAD_DIR = '/data/download';
    const PATH_GALLERY_DIR = '/data/gallery';
    const PATH_TEAM_DIR = '/data/teams';

    const NEWS_PAGE_COUNT = 10;
    const NEWS_SHORT_LENGTH = 80;

    public static function new()
    {
        return new Config;
    }

    public static function asAbsolute(string $path): string
    {
        return self::getRoot() . $path;
    }

    public static function getRoot(): string
    {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    public static function getTeamDir(): string
    {
        return self::asAbsolute(self::PATH_TEAM_DIR);
    }

    public static function getGalleryDir(): string
    {
        return self::asAbsolute(self::PATH_GALLERY_DIR);
    }

    public static function getPostDir(): string
    {
        return self::asAbsolute(self::PATH_POST_DIR);
    }

    public static function getDownloadDir(): string
    {
        return self::asAbsolute(self::PATH_DOWNLOAD_DIR);
    }
}
