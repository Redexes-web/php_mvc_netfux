<?php
define("ABSOLUTE_ROOT_PATH", __DIR__);
define("DS", DIRECTORY_SEPARATOR);
require 'lib/EnvLoader.php';
try {
    //code...
    require 'lib/Router.php';
    require 'lib/Kernel.php';
    require 'lib/Database.php';
    require 'lib/Utils.php';
    $kernel = new Lib\Kernel();
    $kernel->bootstrap();
    $kernel->run();
} catch (\Throwable $th) {
    if ($_ENV["ENV"] === "prod")
        include ABSOLUTE_ROOT_PATH . DS . 'www/views/error/e500View.phtml';
    else
        throw $th;
}
