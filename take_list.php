<?php
//DO NOT DISPLAY ERRORS TO USER
ini_set("display_errors", 1);
ini_set("log_errors", 1);

//Define where do you want the log to go, syslog or a file of your liking with
ini_set("error_log", dirname(__FILE__).'/php_errors.log');

register_shutdown_function(function () {
    $last_error = error_get_last();
    if (!empty($last_error) &&
         $last_error['type'] & (E_ERROR | E_COMPILE_ERROR | E_PARSE | E_CORE_ERROR | E_USER_ERROR)
       ) {
        require_once(dirname(__FILE__).'/505.php');
        exit(1);
    }
});

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
$users = new Users();
$users->login("Zasser", "11221348Was");
ob_start();

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
        $ai = new AI($users);
        $users->SaveList($ai->ListGenerator($_POST["num"]), true);
        header("Location: generate_list.php");
        break;
    case 4:
        $ai = new AI($users);
        $users->SaveList($ai->ListGenerator($_POST["num"]), false);
        header("Location: generate_list.php");
        break;
    case 5:
        $users->deleteList();
        header("Location: generate_list.php");
        break;
    case 6:
        $users->CorrectAI($_GET['status'], $_GET['ID']);
        header("Location: list_tools.php?status=".$_GET['status']);
        die();
        break;
    case 7:
        $users->CorrectAI($_GET['status'], $_GET['ID']);
        header("Location: rand.php?status=".$_GET['status']);
        die();
        break;
    case 8:
        $users->addList($_GET['ID']);
        header("Location: rand.php?status=9");
        die();
        break;

}
