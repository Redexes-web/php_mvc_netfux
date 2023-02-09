<?php

namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Flashbag;
use Lib\EnvLoader;
use App\Entity\Serie;
use App\Models\UserModel;
use App\Models\GenreModel;
use App\Models\SerieModel;
use App\Classes\Validation;
use App\Models\DirectorModel;

class SerieController  extends AbstractController
{
    public function index()
    {
        // Utils::dd((new SerieModel())->find(1, true));
        $flashbag = new Flashbag();

        return $this->render('serie/indexView.phtml', ["series" => (new SerieModel)->findBy([], true)]);
    }

    public function add()
    {
        // Utils::dd((new SerieModel())->find(1, true));
        $flashbag = new Flashbag();
        $editMode = false;
        if (isset($_GET['id']) && (int)htmlspecialchars($_GET['id'])) {
            $serie = (new SerieModel)->find((int)htmlspecialchars($_GET['id']), true);
            $editMode = true;
        }
        $serie = $serie ?? new Serie;
        $directors = (new DirectorModel)->findBy([]);
        $genres = (new GenreModel)->findBy([]);
        if (Utils::postIsSet($_POST)) {
            $directorIds = [];
            $genreIds = [];
            foreach ($directors as $director) {
                $directorIds[] = $director->get('id');
            }
            foreach ($genres as $genre) {
                $genreIds[] = $genre->get('id');
            }
            $serie->set("title", htmlspecialchars(trim($_POST["title"])))
                ->set("description", htmlspecialchars(trim($_POST["description"])))
                ->set("episodeLength", htmlspecialchars(trim($_POST["episodeLength"])))
                ->set("numberOfEpisodes", htmlspecialchars(trim($_POST["numberOfEpisodes"])))
                ->set("directorId", htmlspecialchars(trim($_POST["directorId"])))
                ->set("genreId", htmlspecialchars(trim($_POST["genreId"])))
                ->set("idYoutube", htmlspecialchars(trim($_POST["idYoutube"])))
                ->set("publishedAt", htmlspecialchars(trim($_POST["publishedAt"])));
            !isset($editMode) || $_FILES["picture"]["name"] != "" ? $serie->set("picture", $_FILES["picture"]) : null;
            $val = new Validation();
            $val->name('Titre')->value($serie->get('title'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Description')->value($serie->get('description'))->pattern('text')->min(2)->max(3000)->required();
            $val->name('Durée du film')->value($serie->get('episodeLength'))->pattern('int')->required();
            $val->name('Durée du film')->value($serie->get('numberOfEpisodes'))->pattern('int')->required();
            $val->name('Directeur')->value($serie->get('directorId'))->inArray($directorIds)->required();
            $val->name('Genre')->value($serie->get('genreId'))->inArray($genreIds)->required();
            $val->name('Id Youtube')->value($serie->get('idYoutube'))->pattern('alphanum')->min(2)->max(100)->required();
            $val->name('Date de sortie')->value($serie->get('publishedAt'))->pattern('date_ymd');
            $val->name('Image')->value($serie->get('picture'))->pattern('file', $serie->get('picture'), !$editMode);
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('serie/newView.phtml', [
                    "serie" => $serie,
                    'genres' => $genres,
                    'directors' => $directors,
                    'editMode' => $editMode
                ]);
            }
            if (!isset($editMode) || $_FILES["picture"]["name"]) {
                $fileExt = '.' . strtolower(pathinfo(basename($_FILES["picture"]["name"]), PATHINFO_EXTENSION));
                $fileName = date('YmdHis') . uniqid() . $fileExt;
                $targetFile = ABSOLUTE_ROOT_PATH . EnvLoader::get('UPLOAD_DIR') . $fileName;
                move_uploaded_file($_FILES["picture"]["tmp_name"], $targetFile);
                $serie->set('picture', $fileName);
            }
            if ($serie->get('createdAt') === null) {
                $serie->set('createdAt', new \DateTime());
            }
            $serie->set('updatedAt', new \DateTime());
            $serieModel = new SerieModel();
            $id = $serieModel->add($serie);
            return $this->redirectToRoute('app_serie_index');
        }
        return $this->render('serie/newView.phtml', [
            "serie" => $serie,
            'genres' => $genres,
            'directors' => $directors,
            'editMode' => $editMode
        ]);
    }

    public function show()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_serie_index');
        }
        $serie = (new SerieModel)->find((int)$_GET['id']);
        if (!$serie) {
            return $this->redirectToRoute('app_serie_index');
        }
        return $this->render('serie/showView.phtml', ['serie' => $serie]);
    }

    public function delete()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_serie_index');
        }
        $serieModel = new SerieModel;
        $serie = $serieModel->find((int)$_GET['id']);
        if (!$serie) {
            return $this->redirectToRoute('app_serie_index');
        }
        $serieModel->remove($serie);
        $flashbag = new Flashbag();
        $flashbag->addMessage($serie->get('title') . " supprimé avec succès");

        return $this->redirectToRoute('app_serie_index');
    }
}
