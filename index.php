<?php
require __DIR__ . '/assets/html/head.html';
use Sesshin\User\Session as UserSession;
use Sesshin\Store\FileStore;

set_time_limit(360);
ini_set('display_errors', 1);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$users->login("Zasser", "11221348Was");
?>




<?php
require __DIR__ . '/assets/html/footer.html';
?>