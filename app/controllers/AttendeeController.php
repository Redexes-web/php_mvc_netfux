<?php


namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Database;
use Lib\Flashbag;
use Lib\EnvLoader;
use App\Entity\User;
use App\Entity\Attendee;
use App\Models\UserModel;
use App\Entity\Speciality;
use App\classes\Validation;
use App\Models\AttendeeModel;
use App\Models\SpecialityModel;

class AttendeeController extends AbstractController
{
    public function index()
    {
        // Utils::dd((new SerieModel())->find(1, true));
        $flashbag = new Flashbag();

        return $this->render('attendee/indexView.phtml', ["attendees" => (new AttendeeModel)->findBy([], true)]);
    }
public function add()
{
    // Utils::dd((new SerieModel())->find(1, true));
    $flashbag = new Flashbag();
    $editMode = false;
    if (isset($_GET['id']) && (int)htmlspecialchars($_GET['id'])) {
        $attendee = (new AttendeeModel)->find((int)htmlspecialchars($_GET['id']), true);
        $editMode = true;
    }
    $attendee = $attendee ?? new Attendee;
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
        // if (!isset($editMode) || $_FILES["picture"]["name"]) {
        //     $fileExt = '.' . strtolower(pathinfo(basename($_FILES["picture"]["name"]), PATHINFO_EXTENSION));
        //     $fileName = date('YmdHis') . uniqid() . $fileExt;
        //     $targetFile = ABSOLUTE_ROOT_PATH . EnvLoader::get('UPLOAD_DIR') . $fileName;
        //     move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile);
        //     $attendee->set('picture', $fileName);
        // }
        // if ($attendee->get('createdAt') === null) {
        //     $attendee->set('createdAt', new \DateTime());
        // }
        // $attendee->set('updatedAt', new \DateTime());
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