<?php

namespace lowebf\Data;

use lowebf\Persistance\IPersistance;

class ContentUnit {
    use StorableTrait;

    /* @var ConfigModule */
	private $configModule;
    /* @var array */
	protected $data = [];
    /* @var string */
	protected $path;
    /* @var IPersistance */
	private $persistance;

	function __construct() {
	}

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     */
    public function loadFromFile(string $path, IPersistance $persistance): ContentUnit {
        throw new \Exception();
    }

    /*
     * @param string $path
     * @param IPersistance $persistance
     * @return ContentUnit
     */
    public function loadFromFileOrCreate(string $path, IPersistance $persistance): ContentUnit {
        throw new \Exception();
    }

    public function __destruct() {
        $this->save();
    }

    public function __get(string $name): mixed {
        if(isset($this->data[$name])) {
            return $this->data[$name];
        }

        return null;
    }

    public function __set(string $name, mixed $value) {
        $this->data[$name] = $value;
    }

    public function __isset(string $name): bool {
        return isset($this->data[$name]);
    }

    public function __unset(string $name) {
        unset($this->data[$name]);
    }

	protected function save() {}
}
