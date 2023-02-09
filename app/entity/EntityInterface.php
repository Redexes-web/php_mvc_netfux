<?php

namespace App\Entity;

/**
 * EntityInterface used to type Entities
 */
interface EntityInterface
{
    public function set($name, $value): self;
    const MANY_TO_ONE = [];
    const ONE_TO_MANY = [];
}
