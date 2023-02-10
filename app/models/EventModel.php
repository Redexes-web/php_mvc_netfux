<?php

namespace App\Models;

use App\Entity\Attendee;
use App\Entity\Event;

class EventModel extends AbstractModel
{
    public function __construct()
    {
        $this->entityName = Event::class;
        parent::__construct();
    }
}
