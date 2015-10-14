<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 13.10.2015
     * Time: 22:42
     */

    //стартуем сессию, если ее еще нет
    session_start();

    require_once "class/Img.php";

    if (isset($_GET['path']) && ($path = $_GET['path'])) {
        $img = new Img($path);
        $img->getImage();
    }

?>


