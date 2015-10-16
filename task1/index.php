<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 13.10.2015
     * Time: 18:04
     */
    //стартуем сессию, если ее еще нет
    session_start();

    include_once "class/Uploader.php";
    $uploader = new Uploader();

    if ($_FILES && array_key_exists("pic", $_FILES)) {
        $uploader->setMaxSize(2 * 1024 * 1024);
        if ($pathInfo = $uploader->upload($_FILES['pic'])) {
            //сохраняем имя файла в сессию юзера
            $_SESSION['files'][] = $pathInfo['name'];
            //редирект на картинку
            header("Location: /task1/pic.php?path=/uploads/".$pathInfo['name']);
        } else {
            //вывод ошибок
            print_r($uploader->getErrors());
        }
    }
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="pic" required> <input type="submit" value="upload">
</form>

<ul>
    <?php foreach ($uploader->getListUploads() as $name => $path): ?>
        <li><a href="/task1/pic.php?path=<?= $path ?>"><?= $name ?></a></li>
    <?php endforeach; ?>
</ul>
