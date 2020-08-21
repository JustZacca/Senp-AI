<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
$users = new Users();
$users->login("Zasser", "11221348Was");
$ai = new AI($users);
$action = ($_SERVER['REQUEST_METHOD'] === 'POST') ? $_POST['action'] : $_GET['action'];
switch ($action) {
    case 0:
        $users->getAniList();
        header("Location: user.php");
        die();
        break;
    case 1:
        $users->deletList();
        header("Location: user.php");
        break;
    case 2:
        $users->correctList();
        $users->ID_List();
        header("Location: user.php");
        break;
    case 3:
        $users->SaveList($ai->ListGenerator($_POST["num"]), true);
        header("Location: generate_list.php");
        break;
    case 4:
        $users->SaveList($ai->ListGenerator($_POST["num"]), false);
        header("Location: generate_list.php");
        break;
    case 5:
        $users->deleteList();
        header("Location: generate_list.php");
        break;
}
