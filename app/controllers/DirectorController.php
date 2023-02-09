<?php

namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Database;
use Lib\Flashbag;
use App\Entity\User;
use App\Entity\Director;
use App\Models\UserModel;
use App\classes\Validation;
use App\Models\DirectorModel;
use App\Models\MovieModel;
use App\Models\SerieModel;

class DirectorController extends AbstractController
{
    public function index()
    {
        $directorModel = new DirectorModel();
        $directors = $directorModel->findBy([]);
        $director = new Director;
        if (Utils::postIsSet($_POST)) {
            $director->set("firstName", htmlspecialchars(trim($_POST["firstName"])))
                ->set("lastName", htmlspecialchars(trim($_POST["lastName"])));
            !isset($editMode) || $_FILES["picture"]["name"] != "" ? $director->set("picture", $_FILES["picture"]) : null;
            $val = new Validation();
            $val->name('Titre')->value($director->get('firstName'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Description')->value($director->get('lastName'))->pattern('text')->min(2)->max(3000)->required();
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('director/indexView.phtml', [
                    "director" => $director
                ]);
            }
            $id = $directorModel->add($director);
            return $this->redirectToRoute('app_director_index');
        }
        return $this->render('director/indexView.phtml', ['directors' => $directors, "director" => $director]);
    }

    public function delete()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_director_index');
        }
        $directorModel = new DirectorModel;
        $director = $directorModel->find((int)$_GET['id']);
        if (!$director) {
            return $this->redirectToRoute('app_director_index');
        }
        $serieModel = new SerieModel;
        $movieModel = new MovieModel;
        $movies = $movieModel->findBy(['director_id' => $director->get('id')]);
        $series = $serieModel->findBy(['director_id' => $director->get('id')]);
        foreach ($series as $serie) {
            $serie->set('directorId', null);
            $serieModel->add($serie);
        }
        foreach ($movies as $movie) {
            $movie->set('directorId', null);
            $movieModel->add($movie);
        }
        $directorModel->remove($director);
        $flashbag = new Flashbag();
        $flashbag->addMessage($director->get('firstName') . " " . $director->get('lastName') . " supprimé avec succès");

        return $this->redirectToRoute('app_director_index');
    }
}
