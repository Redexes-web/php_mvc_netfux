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
        '/register' => [
            'controller' => 'Account',
            'method'     => 'register',
            'name'       => 'app_account_register',
        ],
        '/login' => [
            'controller' => 'Account',
            'method'     => 'login',
            'name'       => 'app_account_login',
        ],
        '/profile' => [
            'controller' => 'Account',
            'method'     => 'profile',
            'name'       => 'app_account_profile',
        ],
        '/profile/edit' => [
            'controller' => 'Account',
            'method'     => 'edit',
            'name'       => 'app_account_edit',
        ],
        '/logout' => [
            'controller' => 'Account',
            'method'     => 'logout',
            'name'       => 'app_account_logout',
        ],
        '/events' => [
            'controller' => 'Event',
            'method'     => 'index',
            'name'       => 'app_event_index',
        ],
        '/event/new' => [
            'controller' => 'Event',
            'method'     => 'new',
            'name'       => 'app_event_new',
        ],
        '/event/attend' => [
            'controller' => 'Event',
            'method'     => 'attend',
            'name'       => 'app_event_attend',
        ],
        '/attendee/new' => [
            'controller' => 'Attendee',
            'method'     => 'add',
            'name'       => 'app_attendee_new',
        ],
        '/attendees' => [
            'controller' => 'Attendee',
            'method'     => 'index',
            'name'       => 'app_attendee_index',
        ],
        '/attendee/show' => [
            'controller' => 'Attendee',
            'method'     => 'index',
            'name'       => 'app_attendee_show',
        ],
        '/attendee/edit' => [
            'controller' => 'Attendee',
            'method'     => 'add',
            'name'       => 'app_attendee_edit',
        ],
        '/attendee/delete' => [
            'controller' => 'Attendee',
            'method'     => 'delete',
            'name'       => 'app_attendee_delete',
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
