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
    <li class="breadcrumb-item active" aria-current="page">Generate List</li>
  </ol>
</nav>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Generate list</h1>
        <p class="lead">
            Use this utility to generate your suggest list: apply some filters, otherwise the AI will suggest anything, even Hentai or strange stuff</p>
    </div>
</div>




<?php
require __DIR__ . '/assets/html/footer.html';
?>