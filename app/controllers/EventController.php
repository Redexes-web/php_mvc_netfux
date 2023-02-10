<?php


namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Database;
use Lib\Flashbag;
use Lib\EnvLoader;
use App\Entity\User;
use App\Entity\Event;
use App\Entity\Attendee;
use App\Models\UserModel;
use App\Entity\Speciality;
use App\Models\EventModel;
use App\classes\Validation;
use App\Models\AttendeeModel;
use App\Models\SpecialityModel;

class EventController extends AbstractController
{
    public function index()
    {
        $flashbag = new Flashbag();

        return $this->render('event/indexView.phtml', ["events" => (new EventModel)->findBy([], true)]);
    }
    public function add()
    {
        $flashbag = new Flashbag();
        $editMode = false;
        if (isset($_GET['id']) && (int)htmlspecialchars($_GET['id'])) {
            $event = (new EventModel)->find((int)htmlspecialchars($_GET['id']), true);
            $editMode = true;
        }
        $event = $event ?? new Event;
        $specialities = (new SpecialityModel)->findBy([]);

        if (Utils::postIsSet($_POST)) {
            $specialityIds = [];
            foreach ($specialities as $speciality) {
                $specialityIds[] = $speciality->get('id');
            }
            $event->set("title", htmlspecialchars(trim($_POST["title"])))
                ->set("description", htmlspecialchars(trim($_POST["description"])))
                ->set("startAt", htmlspecialchars(trim($_POST["startAt"])))
                ->set("endAt", htmlspecialchars(trim($_POST["endAt"])));
            $val = new Validation();
            $val->name('Prénom')->value($event->get('title'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Nom')->value($event->get('description'))->pattern('text')->min(2)->max(3000)->required();
            $val->name('Date de naissance')->value($event->get('startAt'))->pattern('date_ymd')->required();
            $val->name('Date de naissance')->value($event->get('endAt'))->pattern('date_ymd')->required();
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('event/newView.phtml', [
                    "event" => $event,
                    'specialities' => $specialities,
                    'editMode' => $editMode
                ]);
            }
            // if (!isset($editMode) || $_FILES["picture"]["name"]) {
            //     $fileExt = '.' . strtolower(pathinfo(basename($_FILES["picture"]["name"]), PATHINFO_EXTENSION));
            //     $fileName = date('YmdHis') . uniqid() . $fileExt;
            //     $targetFile = ABSOLUTE_ROOT_PATH . EnvLoader::get('UPLOAD_DIR') . $fileName;
            //     move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile);
            //     $event->set('picture', $fileName);
            // }
            // if ($event->get('createdAt') === null) {
            //     $event->set('createdAt', new \DateTime());
            // }
            // $event->set('updatedAt', new \DateTime());
            $eventModel = new EventModel();
            $id = $eventModel->add($event);
            return $this->redirectToRoute('app_event_index');
        }
        return $this->render('event/newView.phtml', [
            "event" => $event,
            'specialities' => $specialities,
            'editMode' => $editMode
        ]);
    }

    public function attend(){
        $flashbag = new Flashbag();
        $editMode = false;
        if (!isset($_GET['id']) || !(int)htmlspecialchars($_GET['id'])) {
            return $this->redirectToRoute('app_event_index');
        }
        $event = (new EventModel)->find((int)htmlspecialchars($_GET['id']), true);
        if(!$event){
            return $this->redirectToRoute('app_event_index');
        }
        $userSession = (new Session("user"));
        /**
         * @var ?User $user
         */
        $user = $userSession->exist() ? (new UserModel)->find($userSession->get('id'), true) : null;
        if($user){
            $user->addEvent($event);
            (new Flashbag)->addMessage("Vous êtes inscrit à l'évènement");
            return $this->redirectToRoute('app_event_index');
        }
        $event = (new EventModel)->find((int)htmlspecialchars($_GET['id']), true);
        $attendee = new Attendee;
        $specialities = (new SpecialityModel)->findBy([]);
    
        if (Utils::postIsSet($_POST)) {
            $specialityIds = [];
            foreach ($specialities as $speciality) {
                $specialityIds[] = $speciality->get('id');
            }
            $attendee->set("firstName", htmlspecialchars(trim($_POST["firstName"])))
                ->set("lastName", htmlspecialchars(trim($_POST["lastName"])))
                ->set("birthdayAt", htmlspecialchars(trim($_POST["birthdayAt"])))
                ->set("email", htmlspecialchars(trim($_POST["email"])))
                ->set("phone", htmlspecialchars(trim($_POST["phone"])))
                ->set("specialityId", htmlspecialchars(trim($_POST["specialityId"])));
            $val = new Validation();
            $val->name('Prénom')->value($attendee->get('firstName'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Nom')->value($attendee->get('lastName'))->pattern('text')->min(2)->max(3000)->required();
            $val->name('Date de naissance')->value($attendee->get('birthdayAt'))->pattern('date_ymd')->required();
            $val->name('Email')->value($attendee->get('email'))->pattern('email')->required();
            $val->name('Numéro de téléphone')->value($attendee->get('phone'))->pattern('text');
            $val->name('Spécialité')->value($attendee->get('specialityId'))->inArray($specialityIds)->required();
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('attendee/newView.phtml', [
                    "attendee" => $attendee,
                    'specialities' => $specialities,
                    'editMode' => $editMode
                ]);
            }
            $attendeeModel = new AttendeeModel();
            $id = $attendeeModel->add($attendee);
            return $this->redirectToRoute('app_attendee_index');
        }
        return $this->render('attendee/newView.phtml', [
            "attendee" => $attendee,
            'specialities' => $specialities,
            'editMode' => $editMode
        ]);
    }
}