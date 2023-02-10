<?php

namespace App\Models;

use App\Entity\Speciality;

class SpecialityModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Speciality::class;
        parent::__construct();
    }
}
