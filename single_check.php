<?php
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$users->login("Zasser", "11221348Was");
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">AI-Tools</a></li>
    <li class="breadcrumb-item active" aria-current="page">Single Check</li>
  </ol>
</nav>


<?php
$ai = new AI($users);
echo $ai->validity();
?>

<?php
require __DIR__ . '/assets/html/footer.html';
?>