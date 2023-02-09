<?php

namespace App\Models;

use Lib\Utils;
use Lib\Session;
use \Lib\Database;
use App\Entity\Movie;

class MovieModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Movie::class;
        parent::__construct();
    }
}
