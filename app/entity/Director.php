<?php

namespace App\Entity;

class Director extends AbstractEntity
{
    const MANY_TO_ONE = [
        "movies" => Movie::class,
        "series" => Serie::class
    ];
    protected $id;
    protected $firstName;
    protected $lastName;
}
