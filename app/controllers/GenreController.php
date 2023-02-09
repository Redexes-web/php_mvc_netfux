<?php

namespace App\Controllers;

use Lib\Utils;
use Lib\Session;
use Lib\Database;
use Lib\Flashbag;
use App\Entity\User;
use App\Entity\Genre;
use App\Models\UserModel;
use App\classes\Validation;
use App\Models\GenreModel;
use App\Models\MovieModel;
use App\Models\SerieModel;

class GenreController extends AbstractController
{
    public function index()
    {
        $genreModel = new GenreModel();
        $genres = $genreModel->findBy([]);
        $genre = new Genre;
        if (Utils::postIsSet($_POST)) {
            $genre->set("name", htmlspecialchars(trim($_POST["name"])));
            $val = new Validation();
            $val->name('Nom du genre')->value($genre->get('name'))->pattern('text')->min(2)->max(250)->required();
            if (!($val->isSuccess())) {
                new Session("flashbag", $val->getErrors());
                return $this->render('genre/indexView.phtml', [
                    "genre" => $genre
                ]);
            }
            $id = $genreModel->add($genre);
            return $this->redirectToRoute('app_genre_index');
        }
        return $this->render('genre/indexView.phtml', ['genres' => $genres, "genre" => $genre]);
    }

    public function delete()
    {
        if (!$_GET['id'] || !(int)$_GET['id']) {
            return $this->redirectToRoute('app_genre_index');
        }
        $genreModel = new GenreModel;
        $genre = $genreModel->find((int)$_GET['id']);
        if (!$genre) {
            return $this->redirectToRoute('app_genre_index');
        }
        $serieModel = new SerieModel;
        $movieModel = new MovieModel;
        $movies = $movieModel->findBy(['genre_id' => $genre->get('id')]);
        $series = $serieModel->findBy(['genre_id' => $genre->get('id')]);
        foreach ($series as $serie) {
            $serie->set('genreId', null);
            $serieModel->add($serie);
        }
        foreach ($movies as $movie) {
            $movie->set('genreId', null);
            $movieModel->add($movie);
        }
        $genreModel->remove($genre);
        $flashbag = new Flashbag();
        $flashbag->addMessage($genre->get('name')  . " supprimé avec succès");

        return $this->redirectToRoute('app_genre_index');
    }
}
