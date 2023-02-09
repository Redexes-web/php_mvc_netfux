<?php

namespace Lib;

class Utils
{
    /**
     * Recherche un fichier de manière récursive dans un dossier
     * c'est à dire dans le dossier et dans tous ces sous-dossiers.
     * 
     * Récuresive signifie que la fonction se rappelle elle-même indéfiniment tant que cela est nécessaire.
     * Dans le cas d'une recherche dans un dossier et ses sous-dossiers somme ici,
     * Cela veut dire que la fonction s'éxécute une première fois sur le dossier principal.
     * Puis, si le fichier n'est pas trouvé, la fonction se relance elle-même sur tous les sous-dossiers.
     * Si les sous-dossiers contiennent eux-même d'autres sous-dossiers, 
     * elle se relancera encore automatiquement jusqu'à ne plus trouver de sous-dossiers.
     */
    public static function recursiveFileSearch($fileName, $dirName)
    {

        // vérifie que le dossier existe
        if (!is_dir($dirName))
            return null;

        // vérifie si on a trouvé le fichier dans ce dossier
        // dans quel cas on arrête la recherche
        if (file_exists($dirName . '/' . $fileName))
            return $dirName . '/' . $fileName;

        // Cherche dans les sous-dossiers du dossier actuel
        $dir = dir($dirName);
        $foundFile = null;
        while (false !== ($entry = $dir->read())) {

            // passe outre les dossiers '.' et '..'
            if ($entry == '.' || $entry == '..')
                continue;

            // si on a un sous-dossier, lancé la recherche dans celui-ci
            if (is_dir($dirName . '/' . $entry)) {
                $foundFile = self::recursiveFileSearch($fileName, $dirName . '/' . $entry);

                // si on a trouvé le fichier, s'arrêté là
                if ($foundFile)
                    return $foundFile;
            }
        }
        // ferme le dossier
        $dir->close();

        // aucun fichier trouvé dans le dossier en cours ou dans ses sous-dossiers
        return null;
    }
    public static function slugify($string)
    {

        $table = array(
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => '-'
        );

        // -- Remove duplicated spaces
        $stripped = preg_replace(array('/\s{2,}/', '/[\t\n]/'), ' ', $string);

        // -- Returns the slug
        return strtolower(strtr($string, $table));
    }

    public static function snakeToCamelCase($string, $capitalizeFirstCharacter = false)
    {

        $str = str_replace('_', '', ucwords($string, '_'));

        if (!$capitalizeFirstCharacter) {
            $str = lcfirst($str);
        }

        return $str;
    }


    public static function postIsSet(array $post, $notRequired = [])
    {
        if (!isset($post) || empty($post)) {
            return false;
        }
        foreach ($post as $key => $value) {
            if (!isset($value)) {
                if (!in_array($key, $notRequired)) {
                    return false;
                }
            }
        }
        return true;
    }

    public static function dd($datas)
    {
        echo ("<pre style='background:black;color:green'>");
        var_dump($datas);
        echo ("</pre>");
        die();
    }
    public static function dump($datas)
    {
        echo ("<pre style='background:black;color:green'>");
        var_dump($datas);
        echo ("</pre>");
    }

    // Tronquer une chaine de caractère
    public static function trunkString($str, $max)
    {

        if (strlen($str) > $max) {
            // On la raccourci
            $str = substr($str, 0, $max);
            $last_space = strrpos($str, " ");

            // Et on ajouter les ... à la fin de la chaine
            $str = substr($str, 0, $last_space) . "...";
            return $str;
        }
        return $str;
    }

    public static function getAgeFromBirthday($birthday)
    {
        $bday = new \DateTime($birthday);
        $today = new \Datetime(date('Y-m-d'));
        $diff = $today->diff($bday);
        $age = ["y" => $diff->y, "m" => $diff->m, "d" => $diff->d];
        return (object) $age;
    }

    public static function camelCaseToSnakeCase(?string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }

    public static function arrayCamelCaseToSnakeCase(array $array, bool $keyOnly = false): array
    {
        $result = [];
        if ($keyOnly) {
            foreach ($array as $key => $item) {
                $result[self::camelCaseToSnakeCase($key)] = $item;
            }
        } else {
            foreach ($array as $key => $item) {
                $result[$key] = self::camelCaseToSnakeCase($item);
            }
        }
        return $result;
    }

    public static function removeEmptyValuesInArray($array)
    {
        $availableProperties = [];
        foreach ($array as $key => $value) {
            if ($value == null || is_array($value) || is_object($value)) {
                continue;
            }
            $availableProperties[$key] = $value;
        }
        return $availableProperties;
    }
}
