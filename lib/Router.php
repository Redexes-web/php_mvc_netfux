<?php

namespace Lib;

class Router
{

    private $rootUrl;
    private $wwwPath;
    private $localhostPath;
    private $allUrls = [];
    private $allRoutes = [

        '/' => [
            'controller' => 'Home',
            'method'     => 'main',
            'name'       => 'app_home_main',
        ],
        '/login' => [
            'controller' => 'Account',
            'method'     => 'login',
            'name'       => 'app_account_login',
        ],
        '/logout' => [
            'controller' => 'Account',
            'method'     => 'logout',
            'name'       => 'app_account_logout',
        ],
        '/films' => [
            'controller' => 'Movie',
            'method'     => 'index',
            'name'       => 'app_movie_index',
        ],
        '/film/new' => [
            'controller' => 'Movie',
            'method'     => 'add',
            'name'       => 'app_movie_new',
        ],
        '/film/show' => [
            'controller' => 'Movie',
            'method'     => 'show',
            'name'       => 'app_movie_show',
        ],
        '/film/edit' => [
            'controller' => 'Movie',
            'method'     => 'add',
            'name'       => 'app_movie_edit',
        ],
        '/film/delete' => [
            'controller' => 'Movie',
            'method'     => 'delete',
            'name'       => 'app_movie_delete',
        ],
        '/series' => [
            'controller' => 'Serie',
            'method'     => 'index',
            'name'       => 'app_serie_index',
        ],
        '/serie/show' => [
            'controller' => 'Serie',
            'method'     => 'show',
            'name'       => 'app_serie_show',
        ],
        '/serie/new' => [
            'controller' => 'Serie',
            'method'     => 'add',
            'name'       => 'app_serie_new',
        ],
        '/serie/edit' => [
            'controller' => 'Serie',
            'method'     => 'add',
            'name'       => 'app_serie_edit',
        ],
        '/serie/delete' => [
            'controller' => 'Serie',
            'method'     => 'delete',
            'name'       => 'app_serie_delete',
        ],
        '/directors' => [
            'controller' => 'Director',
            'method'     => 'index',
            'name'       => 'app_director_index',
        ],
        '/director/delete' => [
            'controller' => 'Director',
            'method'     => 'delete',
            'name'       => 'app_director_delete',
        ],
        '/genres' => [
            'controller' => 'Genre',
            'method'     => 'index',
            'name'       => 'app_genre_index',
        ],
        '/genre/delete' => [
            'controller' => 'Genre',
            'method'     => 'delete',
            'name'       => 'app_genre_delete',
        ],
        '/500' => [
            'controller' => 'Error',
            'method'     => 'e500',
            'name'       => 'app_error_e500',
        ],
        '/404' => [
            'controller' => 'Error',
            'method'     => 'e404',
            'name'       => 'app_error_e404',
        ],

    ];


    public function __construct()
    {
        $this->rootUrl = '';
        $this->wwwPath = dirname($this->rootUrl) == DIRECTORY_SEPARATOR ? "/www" : dirname($this->rootUrl) . DS . "www";
        $this->localhostPath = $_SERVER['DOCUMENT_ROOT'];
        foreach ($this->allRoutes as $url => $route) {
            $this->allUrls[$route["name"]] = $url;
        }
    }



    public function getRoute($requestPath)
    {
        if (isset($this->allRoutes[$requestPath])) {
            return $this->allRoutes[$requestPath];
        } else {
            return $this->allRoutes['/404'];
            // throw new \ErrorException('URL inconnue : ' . $requestPath);
        }
    }

    public function getWwwPath($absolute = false)
    {
        if ($absolute) {
            return $this->localhostPath . $this->wwwPath;
        } else {
            return $this->wwwPath;
        }
    }

    public function generateUrl($routeName, $parameters = [])
    {
        if (isset($this->allUrls[$routeName])) {
            return $this->rootUrl . $this->allUrls[$routeName] . (empty($parameters) ? null : '?' . http_build_query($parameters));
        } else {
            throw new \ErrorException("Nom de la route inconnue : " . $routeName);
        }
    }

    /**
     * Get the value of allRoutes
     */
    public function getAllRoutes()
    {
        return $this->allRoutes;
    }

    /**
     * Get the value of allUrls
     */
    public function getAllUrls()
    {
        return $this->allUrls;
    }
}
