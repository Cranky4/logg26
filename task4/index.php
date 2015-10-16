<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 7:33
     */

    // подключаем файл конфигурации
    require_once "config.php";

    //подключаем класс юзеров
    require_once "class/User.php";

    //поиск по домену
    if ($domain = $_GET['domain']) {
        $list = User::getList($domain);
    } else {
        //или весь список
        $list = User::getList();
    }

    //добавление юзеров
    if ($email = $_POST['email']) {
        $user = new User($email);

        //редирект на страницу, чтобы очистить пост
        header("Location: /task4");
    }

    //ниже простейшая форма поиска и добавления юзеров
?>

<form method="GET" action="">
    <label for="domainInput"> Поиск по домену почты
        <input id="domainInput" type="text" name="domain" value="<?= $_GET['domain'] ? $_GET['domain'] : "" ?>">
    </label>
</form>

<ul>
    <?php foreach ($list as $item): ?>
        <li><?= $item['uid'] ?> - <?= $item['email'] ?></li>
    <?php endforeach; ?>
</ul>

<form method="POST">
    <label for="userAdd"> Добавить email <input id="userAdd" type="text" name="email"> </label>
</form>

