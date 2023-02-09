<?php

namespace App\Models;

use Lib\Utils;
use Lib\Session;
use \Lib\Database;
use App\Entity\Job;

class JobModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Job::class;
        parent::__construct();
    }
}
