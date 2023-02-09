<?php

namespace App\Controllers;

use Lib\Database;
use Lib\Utils;
use App\classes\Validation;
use App\Entity\User;
use App\Models\UserModel;
use Lib\Session;

class AccountController extends AbstractController
{
    // public function registerUser()
    // {
    //     if ((new Session("user"))->exist()) {
    //         return $this->redirectToRoute('app_account_dashboard');
    //     }
    //     $title = "Inscription";
    //     if (Utils::postIsSet($_POST, ["phone"])) {
    //         $user = new User;
    //         $user->set("firstName", htmlspecialchars(trim($_POST["fname"])))
    //             ->set("lastName", htmlspecialchars(trim($_POST["lname"])))
    //             ->set("email", htmlspecialchars(trim($_POST["email"])))
    //             ->set("password", htmlspecialchars(trim($_POST["password"])))
    //             ->set("street", htmlspecialchars(trim($_POST["street"])))
    //             ->set("postalCode", htmlspecialchars(trim($_POST["postal_code"])))
    //             ->set("city", htmlspecialchars(trim($_POST["city"])))
    //             ->set("phone", htmlspecialchars(trim($_POST["phone"])))
    //             ->set("mobilePhone", htmlspecialchars(trim($_POST["mobile_phone"])));
    //         $val = new Validation();
    //         $val->name('Prenom')->value($user->firstName)->pattern('alpha')->min(2)->max(250)->required();
    //         $val->name('Nom')->value($user->lastName)->pattern('alpha')->min(2)->max(250)->required();
    //         $val->name('Email')->value($user->email)->pattern('email')->min(2)->max(250)->required();
    //         $val->name('Mot de Passe (8 caractères, 1 majuscule et 1 minuscule au minimum)')->value($user->password)->pattern('password')->min(8)->max(128)->required();
    //         $val->name('Rue')->value($user->street)->pattern('address')->min(2)->max(250)->required();
    //         $val->name('Code Postal')->value($user->postalCode)->pattern('int')->min(5)->max(5)->required();
    //         $val->name('Ville')->value($user->city)->pattern('alpha')->min(2)->max(100)->required();
    //         $val->name('Téléphone')->value($user->phone)->pattern('tel');
    //         $val->name('Téléphone Mobile')->value($user->mobilePhone)->pattern('tel')->required();
    //         if (!($val->isSuccess())) {
    //             new Session("flashbag", $val->getErrors());
    //             return $this->render('account/registerView.phtml', ["title" => $title]);
    //         }
    //         $user->set("password", password_hash($user->password, PASSWORD_BCRYPT));
    //         $userModel = new UserModel();
    //         $id = $userModel->add($user);
    //         return ["redirect" => "app_account_login"];
    //     }
    //     return $this->render('account/registerView.phtml', ["title" => $title]);
    // }

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

    public function profile()
    {
        $userSession = (new Session("user"));
        $userModel = new UserModel(new Database);
        $cats = $userModel->getCats($userSession->get("id"));
        $dogs = $userModel->getDogs($userSession->get("id"));
        $cats = Utils::displayAnimalAgeFromBirthday($cats);
        $dogs = Utils::displayAnimalAgeFromBirthday($dogs);
        if (!$userSession->exist()) {
            return ["redirect" => "app_account_login"];
        }
        $title = "Bonjour" . $userSession->get("firstName") . " " . $userSession->get("lastName");
        return [
            "template" =>   [
                "folder" => "account",
                "file"   => "dashboard",
            ],
            "title" => $title,
            "cats" => $cats,
            "dogs" => $dogs
        ];
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
