<?php
namespace App\Entity;

class UserEvent extends AbstractEntity
{
    const ONE_TO_MANY = [
        "userId" => User::class,
        "eventId" => Event::class
    ];
    protected $id;
}
