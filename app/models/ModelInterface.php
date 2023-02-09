<?php

namespace App\Models;

interface ModelInterface
{
    public function find(int $id): object;
}
