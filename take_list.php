<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
$users = new Users();
$users->login("Zasser", "11221348Was");

switch ($_GET['action']) {
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
    case 4:
        return "Dropped";
        break;
    case 6:
        return "Plan to Watch";
        break;
}
