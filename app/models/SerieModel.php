<?php

namespace App\Models;

use App\Entity\Serie;
use Lib\Utils;
use Lib\Session;
use \Lib\Database;

class SerieModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Serie::class;
        parent::__construct();
    }
}
