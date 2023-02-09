<?php

namespace App\Entity;

use Lib\Utils;

class User extends AbstractEntity
{

    protected $id;
    protected $email;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $street;
    protected $postalCode;
    protected $city;
    protected $phone;
    protected $mobilePhone;
    protected $createdAt;
}
