<?php

namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Flashbag;
use Lib\EnvLoader;
use App\Entity\Movie;
use App\Models\UserModel;
use App\Models\GenreModel;
use App\Models\MovieModel;
use App\Classes\Validation;
use App\Models\DirectorModel;

class MovieController  extends AbstractController
{
    public function index()
    {
        // Utils::dd((new MovieModel())->find(1, true));
        $flashbag = new Flashbag();

        return $this->render('movie/indexView.phtml', ["movies" => (new MovieModel)->findBy([], true)]);
    }

    public function add()
    {
        // Utils::dd((new MovieModel())->find(1, true));
        $flashbag = new Flashbag();
        $editMode = false;
        if (isset($_GET['id']) && (int)htmlspecialchars($_GET['id'])) {
            $movie = (new MovieModel)->find((int)htmlspecialchars($_GET['id']), true);
            $editMode = true;
        }
        $movie = $movie ?? new Movie;
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
            $movie->set("title", htmlspecialchars(trim($_POST["title"])))
                ->set("description", htmlspecialchars(trim($_POST["description"])))
                ->set("length", htmlspecialchars(trim($_POST["length"])))
                ->set("directorId", htmlspecialchars(trim($_POST["directorId"])))
                ->set("genreId", htmlspecialchars(trim($_POST["genreId"])))
                ->set("idYoutube", htmlspecialchars(trim($_POST["idYoutube"])))
                ->set("publishedAt", htmlspecialchars(trim($_POST["publishedAt"])));
            !isset($editMode) || $_FILES["picture"]["name"] != "" ? $movie->set("picture", $_FILES["picture"]) : null;
            $val = new Validation();
            $val->name('Titre')->value($movie->get('title'))->pattern('text')->min(2)->max(250)->required();
            $val->name('Description')->value($movie->get('description'))->pattern('text')->min(2)->max(3000)->required();
            $val->name('Durée du film')->value($movie->get('length'))->pattern('int')->required();
            $val->name('Directeur')->value($movie->get('directorId'))->inArray($directorIds)->required();
            $val->name('Genre')->value($movie->get('genreId'))->inArray($genreIds)->required();
            $val->name('Id Youtube')->value($movie->get('idYoutube'))->pattern('alphanum')->min(2)->max(100)->required();
            $val->name('Date de sortie')->value($movie->get('publishedAt'))->pattern('date_ymd');
            $val->name('Image')->value($movie->get('picture'))->pattern('file', $movie->get('picture'), !$editMode);
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('movie/newView.phtml', [
                    "movie" => $movie,
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
                $movie->set('picture', $fileName);
            }
            if ($movie->get('createdAt') === null) {
                $movie->set('createdAt', new \DateTime());
            }
            $movie->set('updatedAt', new \DateTime());
            $movieModel = new MovieModel();
            $id = $movieModel->add($movie);
            return $this->redirectToRoute('app_movie_index');
        }
        return $this->render('movie/newView.phtml', [
            "movie" => $movie,
            'genres' => $genres,
            'directors' => $directors,
            'editMode' => $editMode
        ]);
    }

    public function show()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_movie_index');
        }
        $movie = (new MovieModel)->find((int)$_GET['id']);
        if (!$movie) {
            return $this->redirectToRoute('app_movie_index');
        }
        return $this->render('movie/showView.phtml', ['movie' => $movie]);
    }

    public function delete()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_movie_index');
        }
        $movieModel = new MovieModel;
        $movie = $movieModel->find((int)$_GET['id']);
        if (!$movie) {
            return $this->redirectToRoute('app_movie_index');
        }
        $movieModel->remove($movie);
        $flashbag = new Flashbag();
        $flashbag->addMessage($movie->get('title') . " supprimé avec succès");

        return $this->redirectToRoute('app_movie_index');
    }
}
