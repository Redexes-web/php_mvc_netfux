<?php

namespace App\Entity;

use App\Entity\EntityInterface;

class Genre extends AbstractEntity
{
    const MANY_TO_ONE = [
        "movies" => Movie::class,
        "series" => Serie::class
    ];
    protected $id;
    protected $name;
    protected $movies;
    protected $series;
}
