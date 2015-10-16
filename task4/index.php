<?php
    /**
     * Created by PhpStorm.
     * User: Cranky4
     * Date: 15.10.2015
     * Time: 7:33
     */

    // ���������� ���� ������������
    require_once "config.php";

    //������� ������
    require_once "class/User.php";

    //����� �� ������
    if ($domain = $_GET['domain']) {
        $list = User::getList($domain);
    } else {
        $list = User::getList();
    }

    //���������� ������
    if ($email = $_POST['email']) {
        $user = new User($email);
        header("Location: /task4");
    }

    //���� ���������� ����� ������ � ���������� ������
?>

<form method="GET" action="">
    <label for="domainInput"> ����� �� ������ �����
        <input id="domainInput" type="text" name="domain" value="<?= $_GET['domain'] ? $_GET['domain'] : "" ?>">
    </label>
</form>

<ul>
    <?php foreach ($list as $item): ?>
        <li><?= $item['uid'] ?> - <?= $item['email'] ?></li>
    <?php endforeach; ?>
</ul>

<form method="POST">
    <label for="userAdd"> �������� email <input id="userAdd" type="text" name="email"> </label>
</form>

