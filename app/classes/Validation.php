<?php

namespace App\Classes;

use Lib\Router;
use Lib\Utils;

class Validation
{

    public $patterns = array(
        'uri'           => '[A-Za-z0-9-\/_?&=]+',
        'url'           => '[A-Za-z0-9-:.\/_?&=#]+',
        'alpha'         => '[\p{L}]+',
        'words'         => '[\p{L}\s]+',
        'alphanum'      => '[\p{L}0-9]+',
        'int'           => '[0-9]+',
        'float'         => '[0-9\.,]+',
        'tel'           => '[0-9+\s()-]+',
        'text'          => '[\p{L}0-9\s\-.,;:!"%&()?+\'°#\/@]+',
        'file'          => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+\.[A-Za-z0-9]{2,4}',
        'folder'        => '[\p{L}\s0-9-_!%&()=\[\]#@,.;+]+',
        'address'       => '[\p{L}0-9\s.,()°-]+',
        'date_dmy'      => '[0-9]{1,2}\-[0-9]{1,2}\-[0-9]{4}',
        'date_ymd'      => '[0-9]{4}\-[0-9]{1,2}\-[0-9]{1,2}',
        'email'         => '[a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5}',
        'password'      => '^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$'
    );


    public $errors = array();


    public function name($name)
    {

        $this->name = $name;
        return $this;
    }


    public function value($value)
    {

        $this->value = $value;
        return $this;
    }

    public function file($value)
    {

        $this->file = $value;
        return $this;
    }


    public function pattern($name, $files = null, $requiredfile = false)
    {

        if ($name == 'array') {

            if (!is_array($this->value)) {
                $this->errors[] = 'Champs ' . $this->name . ' non valide.';
            }
        } elseif ($name == "file" && $files != null) {
            if (!$requiredfile) {
                return $this;
            }
            // Get Image Dimension
            $fileinfo = @getimagesize($files["tmp_name"]);
            if (!$fileinfo) {
                $this->errors[] = 'Champs ' . $this->name . ' non valide.';
                return $this;
            }
            $width = $fileinfo[0];
            $height = $fileinfo[1];

            $allowed_image_extension = array(
                "png",
                "jpg",
                "jpeg",
                "webp"
            );

            // Get image file extension
            $file_extension = pathinfo($files["name"], PATHINFO_EXTENSION);
            // Validate file input to check if is not empty
            if (!file_exists($files["tmp_name"])) {
                $this->errors[] = "Choose image file to upload.";
            }    // Validate file input to check if is with valid extension
            else if (!in_array($file_extension, $allowed_image_extension)) {
                $this->errors[] = "Upload valiid images. Only PNG and JPEG are allowed.";
            }    // Validate image file size
            else if (($files["size"] > 2000000)) {
                $this->errors[] = "Image size exceeds 2MB";
            }    // Validate image file dimension
            else if ($width > "2500" || $height > "3000") {
                $this->errors[] = "Image dimension should be within 2500x3000";
            }
        } else {

            $regex = '/^(' . $this->patterns[$name] . ')$/u';
            if (!is_array($this->value) && $this->value != '' && !preg_match($regex, $this->value)) {
                $this->errors[] = 'Champs ' . $this->name . ' non valide.';
            }
        }
        return $this;
    }


    public function customPattern($pattern)
    {

        $regex = '/^(' . $pattern . ')$/u';
        if ($this->value != '' && !preg_match($regex, $this->value)) {
            $this->errors[] = 'Champs ' . $this->name . ' non valide.';
        }
        return $this;
    }


    public function required()
    {

        if ((isset($this->file) && $this->file['error'] == 4) || ($this->value == '' || $this->value == null)) {
            $this->errors[] = 'Champs ' . $this->name . ' non valide.';
        }
        return $this;
    }


    public function min($length)
    {

        if (is_string($this->value)) {

            if (strlen($this->value) < $length) {
                $this->errors[] = 'Valeur du champs ' . $this->name . 'est inferieur a ' . $length;
            }
        } else {

            if ($this->value < $length) {
                $this->errors[] = 'Valeur du champs ' . $this->name . 'est inferieur a ' . $length;
            }
        }
        return $this;
    }


    public function max($length)
    {

        if (is_string($this->value)) {

            if (strlen($this->value) > $length) {
                $this->errors[] = 'Valeur du champs ' . $this->name . 'est supperieur a ' . $length;
            }
        } else {

            if ($this->value > $length) {
                $this->errors[] = 'Valeur du champs ' . $this->name . 'est supperieur a ' . $length;
            }
        }
        return $this;
    }


    public function equal($value)
    {

        if ($this->value != $value) {
            $this->errors[] = 'Valeurs du champs ' . $this->name . ' nne corresponds pas.';
        }
        return $this;
    }


    public function maxSize($size)
    {

        if ($this->file['error'] != 4 && $this->file['size'] > $size) {
            $this->errors[] = 'Il file ' . $this->name . ' supera la dimensione massima di ' . number_format($size / 1048576, 2) . ' MB.';
        }
        return $this;
    }


    public function ext($extension)
    {

        if ($this->file['error'] != 4 && pathinfo($this->file['name'], PATHINFO_EXTENSION) != $extension && strtoupper(pathinfo($this->file['name'], PATHINFO_EXTENSION)) != $extension) {
            $this->errors[] = 'Il file ' . $this->name . ' non è un ' . $extension . '.';
        }
        return $this;
    }


    public function purify($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }


    public function isSuccess()
    {
        if (empty($this->errors)) return true;
    }


    public function getErrors()
    {
        if (!$this->isSuccess()) return $this->errors;
    }


    public function displayErrors()
    {

        $html = '<ul>';
        foreach ($this->getErrors() as $error) {
            $html .= '<li>' . $error . '</li>';
        }
        $html .= '</ul>';

        return $html;
    }


    public function result()
    {

        if (!$this->isSuccess()) {

            foreach ($this->getErrors() as $error) {
                echo "$error\n";
            }
            exit;
        } else {
            return true;
        }
    }


    public static function is_int($value)
    {
        if (filter_var($value, FILTER_VALIDATE_INT)) return true;
    }


    public static function is_float($value)
    {
        if (filter_var($value, FILTER_VALIDATE_FLOAT)) return true;
    }


    public static function is_alpha($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z]+$/")))) return true;
    }

    public static function is_alphanum($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) return true;
    }


    public static function is_url($value)
    {
        if (filter_var($value, FILTER_VALIDATE_URL)) return true;
    }


    public static function is_uri($value)
    {
        if (filter_var($value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) return true;
    }


    public static function is_bool($value)
    {
        if (is_bool(filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) return true;
    }

    public static function is_email($value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) return true;
    }

    public function inArray($array)
    {
        if (!in_array($this->value, $array))
            $this->errors[] = "Le champs $this->name est incorect";
        return $this;
    }

    public function notFutureDate()
    {

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $this->value);
        $today = \DateTimeImmutable::createFromMutable(new \DateTime());
        if ($date->getTimestamp() > $today->getTimestamp())
            $this->errors[] = "Le champs $this->name est incorect, veuillez entrer une date valide";
        return $this;
    }
}
