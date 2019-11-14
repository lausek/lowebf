<?php

declare(strict_types=1);

namespace lowebf;

class ContentGroup implements \Iterator
{
    private $_path;
    private $_subitems = [];
    private $_array_comps = [];

    public function __construct($path)
    {
        $this->_path = $path;
        assert(is_dir($this->_path));
        foreach (Util::read_dir($this->_path) as $item) {
            $matches = [];
            $full_path = "${path}/${item}";
            $is_dir = is_dir($full_path);

            // idx is the key e.g. 01-team.json
            if (! $is_dir && 1 === preg_match('/(\d+)-(\S+)\.(\S+)/', $item, $matches)) {
                $idx = (int) ($matches[1]);
                $this->_array_comps[$idx] = [$matches[2], $full_path];
            }
            // date is the key e.g. 2018-1-1-my-post-title.md
            elseif (! $is_dir && 1 === preg_match('(\d{4}-\d{1,2}-\d{1,2})-(\S+)\.(\S+)', $item, $matches)) {
                $date = $matches[1];
                $this->_array_comps[$date] = [$matches[2], $full_path];
            } else {
                $this->_subitems[$item] = $is_dir;
            }
        }
    }

    public function __isset($name): bool
    {
        return isset($this->_subitems[$name]);
    }

    public function __get($name)
    {
        assert($this->__isset($name));
        $path = $this->_path . "/${name}";
        if ($this->_subitems[$name]) {
            return new self($path);
        }

        return Util::load_file($path);
    }

    public function current(): void
    {
        $obj = current($this->_array_comps);
        if (false === $obj) {
            return;
        }
        if (! isset($obj[2])) {
            $obj[2] = Util::load_file($obj[1]);
        }

        return $obj[2];
    }

    public function next(): void
    {
        $obj = next($this->_array_comps);
        if (false === $obj) {
            return;
        }
        if (! isset($obj[2])) {
            $obj[2] = Util::load_file($obj[1]);
        }

        return $obj[2];
    }

    public function key()
    {
        return key($this->_array_comps);
    }

    public function valid()
    {
        $key = key($this->_array_comps);

        return null !== $key && false !== $key;
    }

    public function rewind(): void
    {
        reset($this->_array_comps);
    }
}
