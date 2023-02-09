<?php

namespace App\Controllers;

use App\Models\GenreModel;
use App\Models\MovieModel;
use App\Models\UserModel;
use Lib\Flashbag;
use Lib\Utils;

class HomeController  extends AbstractController
{
    public function main()
    {
        // Utils::dd((new MovieModel())->find(1, true));
        $flashbag = new Flashbag();
        return $this->render('home/mainView.phtml', ["hello" => "world"]);
    }
}
