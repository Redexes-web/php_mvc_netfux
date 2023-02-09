<?php

namespace App\Entity;

use Lib\Utils;

abstract class AbstractEntity implements EntityInterface
{
    public function set($name, $value): self
    {
        Utils::dd($this);
        $this->$name = $value;
        return $this;
    }
    public function get($name)
    {
        return $this->$name ?? null;
    }
}
