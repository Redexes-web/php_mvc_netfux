<?php

namespace App\Models;

use App\Entity\UserEvent;

class UserEventModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = UserEvent::class;
        parent::__construct();
    }
}
