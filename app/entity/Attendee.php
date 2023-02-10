<?php

namespace App\Entity;

class Attendee extends AbstractEntity
{
    const ONE_TO_MANY = [
        'specialityId' => Speciality::class,
        'eventId' => Event::class
    ];
    protected $id;
    protected $firstName;
    protected $lastName;
    protected $birthdayAt;
    protected $email;
    protected $phone;
    protected $specialityId;
}
