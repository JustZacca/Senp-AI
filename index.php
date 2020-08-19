<?php
use Sesshin\User\Session as UserSession;
use Sesshin\Store\FileStore;
$userSession = new UserSession(new FileStore(__DIR___.'/static/session/'));

$userSession->create();

set_time_limit(360);
ini_set('display_errors', 1);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';

$users = new Users();
$users->login("Zasser", "11221348Was");
?>
