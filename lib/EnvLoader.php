<?php

namespace Lib;


class EnvLoader
{
    public function __construct()
    {
        $configurationSettings = array();
        if (file_exists(ABSOLUTE_ROOT_PATH . '/.env')) {
            $res = [];
            $envContent = explode("\r\n", file_get_contents(ABSOLUTE_ROOT_PATH . '/.env'));
            foreach ($envContent as $line) {
                $envContentParts = explode(PHP_EOL, $line);
                foreach ($envContentParts as $part) {
                    $temp = explode('=', $part, 2);
                    $val = trim($temp[1], '"');
                    $val = trim($temp[1], "'");
                    $_ENV[$temp[0]] = $val;
                }
            }
        } else {
            throw new \Exception("vous devez creer un fichier .env a la racine de votre projet", 1);
        }
    }

    public static function get($envVarName)
    {
        return isset($_ENV[$envVarName]) ? $_ENV[$envVarName] : null;
    }
}
new EnvLoader();
