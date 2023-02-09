<?php

namespace App\Controllers;

class ErrorController extends AbstractController
{
    public function e500()
    {
        return $this->render('error/e500View.phtml', ['_raw' => true]);
    }
    public function e404()
    {
        return $this->render('error/e404View.phtml', ['_raw' => true]);
    }
}
