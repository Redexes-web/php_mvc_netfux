<?php

namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Database;
use App\Entity\User;
use App\Models\UserModel;
use App\classes\Validation;
use App\Models\SpecialityModel;

class AccountController extends AbstractController
{
    public function register()
    {
        if ((new Session("user"))->exist()) {
            return $this->redirectToRoute('app_account_dashboard');
        }
        $title = "Inscription";
        if (Utils::postIsSet($_POST, ["phone"])) {
            $user = new User;
            $user->set("firstName", htmlspecialchars(trim($_POST["fname"])))
                ->set("lastName", htmlspecialchars(trim($_POST["lname"])))
                ->set("email", htmlspecialchars(trim($_POST["email"])))
                ->set("password", htmlspecialchars(trim($_POST["password"])));
            $val = new Validation();
            $val->name('Prenom')->value($user->get("firstName"))->pattern('alpha')->min(2)->max(250)->required();
            $val->name('Nom')->value($user->get("lastName"))->pattern('alpha')->min(2)->max(250)->required();
            $val->name('Email')->value($user->get("email"))->pattern('email')->min(2)->max(250)->required();
            $val->name('Mot de Passe (8 caractères, 1 majuscule et 1 minuscule au minimum)')->value($user->get("password"))->pattern('password')->min(8)->max(128)->required();
            if((new UserModel)->findBy(["email" => $user->get("email")])){
                $val->addError("Email déja utilisé");
            }
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('account/registerView.phtml', ["title" => $title, "registering" => $user]);
            }
            $user->set("password", password_hash($user->get("password"), PASSWORD_BCRYPT));
            $userModel = new UserModel();
            $id = $userModel->add($user);
            return $this->redirectToRoute("app_account_login");
        }
        return $this->render('account/registerView.phtml', ["title" => $title]);
    }

    public function login()
    {
        if ((new Session("user"))->exist()) {
            return $this->redirectToRoute("app_home_main");
        }
        $title = "Connexion";
        if (isset($_POST["email"]) && isset($_POST["password"])) {
            $val = new Validation();
            $val->name('Email ')->value($_POST["email"])->pattern('email')->min(2)->max(250)->required();
            if (!$val->isSuccess()) {
                new Session("flashbag", $val->getErrors());
                return $this->redirectToRoute("app_account_login");
            }
            $userModel = new UserModel();
            $user = $userModel->findByEmailAndCheck(htmlspecialchars(trim($_POST["email"])), htmlspecialchars(trim($_POST["password"])));
            if ($user) {
                new Session("user", $user, ["password"]);
                unset($user);
                
                return $this->redirectToRoute("app_home_main");
            }
        }
        return $this->render('account/loginView.phtml', [
            "title" => $title,
        ]);
    }

    public function edit()
    {
        $userId = (new Session("user"))->exist() ? (new Session("user"))->get("id") : null;
        if (!$userId) {
            return $this->redirectToRoute("app_account_login");
        }
        $user = (new UserModel)->find($userId, true);
        $specialities = (new SpecialityModel)->findBy([]);
        if (Utils::postIsSet($_POST)) {
            $specialityIds = [];
            foreach ($specialities as $speciality) {
                $specialityIds[] = $speciality->get('id');
            }
            $user
                ->set("birthdayAt", htmlspecialchars(trim($_POST["birthdayAt"])))
                ->set("phone", htmlspecialchars(trim($_POST["phone"])))
                ->set("specialityId", htmlspecialchars(trim($_POST["specialityId"])))
                ->set("firstName", htmlspecialchars(trim($_POST["firstName"])))
                ->set("lastName", htmlspecialchars(trim($_POST["lastName"])))
                ->set("email", htmlspecialchars(trim($_POST["email"])))
                ->set("password", htmlspecialchars(trim($_POST["password"])));
            $val = new Validation();
           
            $val->name('Date de naissance')->value($user->get('birthdayAt'))->pattern('date_ymd');
            $val->name('Numéro de téléphone')->value($user->get('phone'))->pattern('text');
            $val->name('Prénom')->value($user->get('firstName'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Nom')->value($user->get('lastName'))->pattern('text')->min(2)->max(3000)->required();
            $val->name('Email')->value($user->get('email'))->pattern('email')->min(2)->max(250)->required();
            $val->name('Mot de Passe (8 caractères, 1 majuscule et 1 minuscule au minimum)')->value($user->get("password"))->pattern('password')->min(8)->max(128)->required();
            $val->name('Spécialité')->value($user->get('specialityId'))->inArray($specialityIds);
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('account/editView.phtml', [
                    "me" => $user,
                    'specialities' => $specialities,
                    'editMode' => true
                ]);
            }
            $userModel = new UserModel();
            $id = $userModel->add($user);
            return $this->redirectToRoute('app_attendee_index');
        }
        return $this->render('account/editView.phtml', [
            "me" => $user,
            'specialities' => $specialities,
            'editMode' => true
        ]);
    }
    
    public function logout()
    {
        $userSession = (new Session("user"));
        if (!$userSession->exist()) {
            return $this->redirectToRoute('app_home_main');
        }
        $userSession->destroy();
        return $this->redirectToRoute('app_home_main');
    }
}
