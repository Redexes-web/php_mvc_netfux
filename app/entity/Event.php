<?php

namespace App\Entity;

use Lib\Utils;
use App\Models\UserModel;
use App\Models\UserEventModel;

class Event extends AbstractEntity
{
    const MANY_TO_ONE = [
        "userEvents" => UserEvent::class
    ];
    protected $id;
    protected $title;
    protected $description;
    protected $startAt;
    protected $endAt;
    protected $users = [];
    public function get($name){
        if($name === "users"){
            if(!empty($this->users)){
                return $this->users;
            }
            $model = new UserEventModel();
            $userModel = new UserModel();
            $userEvents = $model->findBy(["event_id" => $this->id]);
            foreach($userEvents as $userEvent){
                $this->users[] = $userModel->findBy(["id"=>$userEvent->get("userId")]);
            }
            return $this->users;
        }

        return parent::get($name);
    }
}
