<?php

namespace Lib;

use App\Entity\User;


class Session
{
    private $key;


    public function __construct($key, $values = null, $keyToAvoid = [])
    {
        $this->key = $key;
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (is_array($values)) {
            foreach ($values as $varName => $value) {
                if (!in_array($varName, $keyToAvoid)) {
                    $_SESSION[$this->key][$varName] = $value;
                }
            }
        }
        else if (is_object($values)) {
            method_exists($values,"toArray") ? $values = $values->toArray() : "";
            foreach ($values as $varName => $value) {
                if (!in_array($varName, $keyToAvoid)) {
                    $_SESSION[$this->key][$varName] = $value;
                }
            }
        }
        else{
            if($values != null)
            $_SESSION[$this->key] = [$values];
        }
    }


    public function destroy($key = null)
    {
        if ($key)
            $_SESSION[$this->key][$key] = [];
        else
            $_SESSION[$this->key] = [];
    }



    public function exist($key = null)     // qd fonction commence par as ou is, la réponse est tjrs un booleen
    {
        if (isset($key))
            return (isset($_SESSION[$this->key][$key])) && (!empty($_SESSION[$this->key][$key]));
        else
            return (isset($_SESSION[$this->key])) && (!empty($_SESSION[$this->key]));
    }


    public function get($key = null)
    {
        if (!$this->exist($key)) {
            return false;
        }
        if ($key)
            return $_SESSION[$this->key][$key];
        else
            return $_SESSION[$this->key];
    }
    public function getAndDelete()
    {
        $content = $_SESSION[$this->key];
        $this->destroy();
        return $content;
    }
    public function set($values = [])
    {
        foreach ($values as $varName => $value) {
            $_SESSION[$this->key][$varName] = $value;
        }
    }
}
