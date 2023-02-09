<?php

namespace App\Models;

use Lib\Utils;
use Lib\Session;
use \Lib\Database;
use App\Entity\Genre;

class GenreModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Genre::class;
        parent::__construct();
    }
}
