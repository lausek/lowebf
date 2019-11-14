<?php

namespace lowebf;

class ContentGroup implements \Iterator {
    private $_path;
    private $_subitems = [];
    private $_array_comps = [];

    public function __construct($path)
    {
        $this->_path = $path;
        assert(is_dir($this->_path));
        foreach(Util::read_dir($this->_path) as $item)
        {
            $matches = [];
            $full_path = "$path/$item";
            $is_dir = is_dir($full_path);

            // idx is the key e.g. 01-team.json
            if(!$is_dir && preg_match('/(\d+)-(\S+)\.(\S+)/', $item, $matches) === 1)
            {
                $idx = intval($matches[1]);
                $this->_array_comps[$idx] = [$matches[2], $full_path];
            }
            // date is the key e.g. 2018-1-1-my-post-title.md
            else if(!$is_dir && preg_match('(\d{4}-\d{1,2}-\d{1,2})-(\S+)\.(\S+)', $item, $matches) === 1)
            {
                $date = $matches[1];
                $this->_array_comps[$date] = [$matches[2], $full_path];
            }
            else
            {
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
        $path = $this->_path . "/$name";
        if($this->_subitems[$name])
        {
            return new ContentGroup($path);
        }
        else
        {
            return Util::load_file($path);
        }
    }

    public function current()
    {
        $obj = current($this->_array_comps);
        if($obj === false)
        {
            return;
        }
        if(!isset($obj[2]))
        {
            $obj[2] = Util::load_file($obj[1]);
        }
        return $obj[2];
    }

    public function next()
    {
        $obj = next($this->_array_comps);
        if($obj === false)
        {
            return;
        }
        if(!isset($obj[2]))
        {
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
        return $key !== null && $key !== false;
    }

    public function rewind()
    {
        reset($this->_array_comps);
    }
}
