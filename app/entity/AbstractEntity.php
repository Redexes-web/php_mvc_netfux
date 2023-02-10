<?php

namespace App\Entity;

use Countable;
use Lib\Utils;

abstract class AbstractEntity implements EntityInterface, Countable
{
    public function set($name, $value): self
    {
        $this->$name = $value;
        return $this;
    }
    public function get($name)
    {
        return $this->$name ?? null;
    }

    public function count(): int
    {
        $count = 0;
        foreach($this as $key => $value){
            $count++;
        }
        return $count;
    }
    public function toArray():array{
        $res= [];
        foreach ($this as $key => $value) {
            $res[$key] = $value;
        }
        return $res;
    }
}
