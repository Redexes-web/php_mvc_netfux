<?php

namespace App\Entity;

use Lib\Utils;
use App\Models\EventModel;
use App\Models\UserEventModel;

class User extends AbstractEntity
{
    const ONE_TO_MANY = [
        'specialityId' => Speciality::class
    ];
    const MANY_TO_ONE = [
        "userEvents" => UserEvent::class
    ];
    protected $id;
    protected $email;
    protected $password;
    protected $firstName;
    protected $lastName;
    protected $birthdayAt;
    protected $phone;
    protected $specialityId;
    protected $isAdmin;
    protected $events = [];

    public function addEvent(Event $event){
        if(!$this->id || !$event->get("id")){
            throw new \Exception("You try to add a relation on non-existent entity in database", 1);
            
        }
        $relationEntity = (new UserEvent)->set("userId", $this->id)->set("eventId", $event->get("id"));
        $model = new UserEventModel();
        if(!$model->findBy(["user_id" => $this->id, "event_id" => $event->get("id")])){
            $model->add($relationEntity);
        }
        return $this;

    }
    public function removeEvent(Event $event){
        if(!$this->id || !$event->get("id")){
            throw new \Exception("You try to add a relation on non-existent entity in database", 1);
            
        }
        $model = new UserEventModel();
        $relationEntity = $model->findBy(["user" => $this, "event" => $event]);
        if(empty($relationEntity)){
            throw new \Exception("You try to remove a relation that doesn't exist", 1);
        }
        else{
            $relationEntity = $relationEntity[0];
        }
        $model->remove($relationEntity);
        return $this;
    }

    public function get($name){
        if($name === "events"){
            $model = new UserEventModel();
            $eventModel = new EventModel();
            $userEvents = $model->findBy(["user_id" => $this->id], true);
            foreach($userEvents as $userEvent){
                $this->events[] = $eventModel->findBy(["id"=>$userEvent->get("eventId")]);
            }
            return $this->events;
        }
        return parent::get($name);
    }
}
