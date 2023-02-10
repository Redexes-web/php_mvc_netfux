<?php

namespace App\Entity;

class Speciality extends AbstractEntity
{
    const MANY_TO_ONE = [
        "attendees" => Attendee::class
    ];
    protected $id;
    protected $name;
}
