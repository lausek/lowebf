<?php

namespace lowebf\Data;

use function lowebf \getFileType;
use lowebf \Environment;
use lowebf \Error \FileNotFoundException;
use lowebf \Error \NotPersistableException;
use lowebf \Parser \Markdown;
use lowebf \Persistance \IPersistance;
use lowebf \Persistance \PersistorJson;
use lowebf \Persistance \PersistorMarkdown;
use lowebf \Persistance \PersistorYaml;
use lowebf \Result;

function unparse_url($parsed_url)
{
    $scheme   = isset($parsed_url['scheme']) ? $parsed_url['scheme'] . '://' : '';
    $host     = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $port     = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $user     = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $pass     = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $pass     = ($user || $pass) ? "$pass@" : '';
    $path     = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $query    = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $fragment = isset($parsed_url['fragment']) ? '#' . $parsed_url['fragment'] : '';
    return "$scheme$user$pass$host$port$path$query$fragment";
}

class ContentUnit
{
    /** @var Environment */
    private $env;
    /** @var array */
	    protected $data = [];
    /** @var string */
	    protected $path;
    /** @var IPersistance */
	    private $persistance = null;

    /**
     * @throws NotPersistableException
     * */
    function __construct(Environment $env, string $path, array $data, IPersistance $persistance = null)
    {
        $this->env = $env;
        $this->data = $data;
        $this->path = $path;
        $this->persistance = $persistance;

        if ($this->persistance === null) {
            $this->persistance = PersistanceJson::getInstance();
        }
    }

    public static function getPersistorFromPath(string $path) : IPersistance
    {
        $fileType = getFileType($path);

        switch ($fileType) {
            case "yml":
            // fallthrough
            case "yaml":
                return PersistorYaml::getInstance();

            case "md":
            // fallthrough
            case "markdown":
                return PersistorMarkdown::getInstance();

            case "json":
                return PersistorJson::getInstance();
        }

        throw new NotPersistableException($fileType);
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return Result<ContentUnit>
     */
    public static function loadFromFile(Environment $env, string $path, IPersistance $persistance = null) : Result
    {
        try {
            if ($persistance === null) {
                $persistance = self::getPersistorFromPath($path);
            }

            $data = $persistance->load($env, $path)->unwrap();
        } catch (\Exception $e) {
            return Result::error($e);
        }

        $contentUnit = new ContentUnit($env, $path, $data, $persistance);
        return Result::ok($contentUnit);
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     * @throws NotPersistableException
     */
    public static function loadFromFileOrCreate(Environment $env, string $path, IPersistance $persistance = null) : ContentUnit
    {
        $loadFileResult = self::loadFromFile($env, $path, $persistance);
        if ($loadFileResult->isOk()) {
            return $loadFileResult->unwrap();
        }

        try {
            $persistance = self::getPersistorFromPath($path);
            $data = $persistance->load($env, $path)->unwrap();
        } catch (FileNotFoundException $e) {
            // ignore exception if loading fails
            $data = [];
        }

        return new ContentUnit($env, $path, $data, $persistance);
    }

    public function __get(string $name)
    {
        return $this->get($name);
    }

    public function __isset(string $name) : bool
    {
        return $this->exists($name);
    }

    public function &asArray() : array
    {
        return $this->data;
    }

    public function get(string $name, $default = null)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return $default;
    }

    public function &getReferenceOrInsert(string $name, $default = null)
    {
        if (!isset($this->data[$name])) {
            $this->data[$name] = $default;
        }

        return $this->data[$name];
    }

    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    public function exists(string $name) : bool
    {
        return isset($this->data[$name]);
    }

    public function unset(string $name) {
        unset($this->data[$name]);
    }

    public function getContentRaw() : string
    {
        return $this->get("content");
    }

    public function getContent(bool $useAbsoluteUrls = false) : string
    {
        $markdown = new Markdown($this->env);

        if ($useAbsoluteUrls) {
            $env = $this->env;

            $markdown->url_filter_func = function($url) use ($env) {
                $components = parse_url($url);

                if (!isset($components["host"]) || empty($components["host"])) {
                    $components["host"] = $env->route()->getServerName();
                }

                if (!isset($components["scheme"]) || empty($components["scheme"])) {
                    $components["scheme"] = $env->route()->getScheme();
                }

                return unparse_url($components);
            };
        }

        return rtrim($markdown->transform($this->getContentRaw()), "\n ");
    }

    public function save()
    {
        $this->persistance->save($this->env, $this->path, $this->data);
    }
}
