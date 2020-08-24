<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$ani = new AniList();
$users->login("Zasser", "11221348Was");
?>
<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="#">AI-Tools</a></li>
    <li class="breadcrumb-item active" aria-current="page">Single Check</li>
  </ol>
</nav>
<br>
<br>
<div class="alert alert-primary" role="alert">
    Use this section to search an Anime and ask to <b>Senp-AI</b> a wisdom about it!
</div>
<form name="form1" id="mainForm" method="post" enctype="multipart/form-data" action="">
<div class="form-row align-items-center">
    <div class="col-auto">
    <label for="formGroupExampleInput">What are you looking for?</label>
    </div>
    <div class="col-auto">
    <input type="text" class="form-control" name ="title" id="title" placeholder="Anime Title">
    </div>
    <div class="col-auto">
    <button type="submit" class="btn btn-primary"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-search" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
  <path fill-rule="evenodd" d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z"/>
  <path fill-rule="evenodd" d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z"/>
</svg></button>
    </div>
    </div>
</form>
<br>
<br>
<?php 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $res = $ani->jikanSearch($_POST['title'])->getResults();
    ?>
<table class="table table-bordered table-responsive table-dark">
  <thead>
    <tr>
      <th scope="col">ID</th>
      <th scope="col">Name</th>
      <th scope="col">IMG</th>
      <th scope="col">Synopsis</th>
    </tr>
  </thead>
  <tbody>
  <?php
    foreach ($res as $res) {
        echo'<tr>
      <th scope="row">'.$res->getMalId().'</th>
      <td>'.$res->getTitle().'</td>
      <td><img src="'.$res->getImageUrl().'" alt="..." width="400px" class="img-fluid"></td>
      <td>'.$res->getSynopsis().'</td>
    </tr>';
    } ?>
  </tbody>
</table>
<?php
} else {
  ?> 
  <div class="text-center">
  <img src="./assets/img/waiting.gif"  class="rounded mx-auto d-block" alt="...">  
  <br>
  <br>
</div>
  </div>
  
 <?php
}
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

}

?>

<?php
require __DIR__ . '/assets/html/footer.html';
?>