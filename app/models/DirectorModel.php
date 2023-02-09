<?php

namespace App\Models;

use App\Entity\Director;
use Lib\Utils;
use Lib\Session;
use \Lib\Database;

class DirectorModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Director::class;
        parent::__construct();
    }
}
