<?php

namespace lowebf\Data;

use lowebf\Environment;
use lowebf\Error\FileNotFoundException;
use lowebf\Error\NotPersistableException;
use lowebf\Persistance\IPersistance;
use lowebf\Persistance\PersistorJson;
use lowebf\Persistance\PersistorMarkdown;
use lowebf\Persistance\PersistorYaml;

class ContentUnit implements IStorable
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
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $extension = strtolower($extension);

        switch ($extension) {
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

        throw new NotPersistableException($extension);
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     * @throws NotPersistableException
     */
    public static function loadFromFile(Environment $env, string $path, IPersistance $persistance = null) : ContentUnit
    {
        if ($persistance === null) {
            $persistance = self::getPersistorFromPath($path);
        }

        $data = $persistance->load($env, $path);
        return new ContentUnit($env, $path, $data, $persistance);
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     * @throws NotPersistableException
     */
    public static function loadFromFileOrCreate(Environment $env, string $path, IPersistance $persistance = null) : ContentUnit
    {
        if ($persistance === null) {
            $persistance = self::getPersistorFromPath($path);
        }

        try {
            $data = $persistance->load($env, $path);
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

    public function save()
    {
        $this->persistance->save($this->env, $this->path, $this->data);
    }
}
