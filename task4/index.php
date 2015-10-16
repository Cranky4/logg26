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
    //шифровальщик
    require_once "class/Encryption.php";
    //подлкючение
    require_once "class/Connection.php";

    $encryption = new Encryption(User::getSecretKey());
    $connection = Connection::getInstance();

    $user = new User($connection, $encryption);

    //поиск по домену
    if ($domain = $_GET['domain']) {
        $list = $user->getList($domain);
    } else {
        //или весь список
        $list = $user->getList();
    }

    //добавление юзеров
    if ($email = $_POST['email']) {
        try {
            $user->addUser($email);
            //редирект на страницу, чтобы очистить пост
            header("Location: /task4");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    //ниже простейшая форма поиска и добавления юзеров
?>

<html>
<head>
    <meta charset="UTF-8"/>
</head>
<body>
<form method="GET" action="">
    <label for="domainInput"> Поиск по домену почты
        <input id="domainInput" type="text" name="domain" value="<?= $_GET['domain'] ? $_GET['domain'] : "" ?>">
    </label> <input type="submit" value="Искать">
</form>

<ul>
    <?php foreach ($list as $item): ?>
        <li><?= $item['uid'] ?> - <?= $item['email'] ?></li>
    <?php endforeach; ?>
</ul>

<form method="POST">
    <label for="userAdd"> Добавить email <input id="userAdd" type="text" name="email"> </label>
    <input type="submit" value="Добавить">
</form>

</body>

</html>



