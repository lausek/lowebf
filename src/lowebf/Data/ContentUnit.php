<?php

namespace lowebf\Data;

use lowebf\Persistance\IPersistance;
use lowebf\Persistance\PersistorJson;
use lowebf\Persistance\PersistorMarkdown;
use lowebf\Persistance\PersistorYaml;

class ContentUnit implements IStorable
{
    /** @var array */
	    protected $data = [];
    /** @var string */
	    protected $path;
    /** @var IPersistance */
	    private $persistance = null;

    function __construct(string $path, array $data, IPersistance $persistance = null)
    {
        $this->data = $data;
        $this->path = $path;
        $this->persistance = $persistance;

        if ($this->persistance === null) {
            $this->persistance = PersistanceJson::getInstance();
        }
    }

    private static function getPersistorFromPath(string $path) : IPersistance
    {
        $extension = pathinfo($path, PATHINFO_EXTENSION);

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
            // fallthrough
            default:
                return PersistorJson::getInstance();
        }
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     */
    public static function loadFromFile(string $path, IPersistance $persistance = null) : ContentUnit
    {
        if ($persistance === null) {
            $persistance = self::getPersistorFromPath($path);
        }

        $data = $persistance->load($path);
        return new ContentUnit($path, $data, $persistance);
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     */
    public static function loadFromFileOrCreate(string $path, IPersistance $persistance = null) : ContentUnit
    {
        if ($persistance === null) {
            $persistance = self::getPersistorFromPath($path);
        }

        try {
            $data = $persistance->load($path);
        } catch (\Throwable $e) {
            // ignore exception if loading fails
            $data = [];
        }

        $contentUnit = new ContentUnit($path, $data, $persistance);
        $contentUnit->save();

        return $contentUnit;
    }

    public function __destruct()
    {
        $this->save();
    }

    public function get(string $name)
    {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    public function set(string $name, $value)
    {
        $this->data[$name] = $value;
    }

    public function exists(string $name) : bool
    {
        return isset($this->data[$name]);
    }

    public function unset(string $name)
    {
        unset($this->data[$name]);
    }

    protected function save()
    {
        $this->persistance->save($this->path, $this->data);
    }
}
