<?php
use Jikan\Jikan;
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$ani = new AniList();
$jikan = new Jikan;
$users->login("Zasser", "11221348Was");
?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="#">AI-Tools</a></li>
        <li class="breadcrumb-item active" aria-current="page">Single Check</li>
    </ol>
</nav>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">List tools</h1>
        <p class="lead">
            This section is for managing your list.</p>
    </div>
</div>

<?php
if (file_exists($users->suggestList()) && $users->suggestCount() != 0) {
    ?>
<div class="alert alert-primary" role="alert">
    Use this section to view the list that <b>Senp-AI</b> has created for you by examining your tastes. Use the button
    to the
    right of each title to tell the AI ​​if he was right or wrong.
</div>
<br>
<div class="container">
<table class="table table-bordered table-responsive table-dark">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col"></th>
            <th scope="col">Genre</th>
            
            <th scope="col">Synopsis</th>
            <th scope="col">Drop</th>
        </tr>
    </thead>
    <tbody>
        <?php
      foreach (json_decode(file_get_contents($users->suggestList()), true) as $anime) {
          $ani->query($anime['ID']);
          $jani = $jikan->Anime($anime['ID']);
          echo '<tr>
        <th scope="row">'.$anime['ID'].'</th>
        <td><a href=https://myanimelist.net/anime/'.$anime['ID'].' target="_blank">'.$ani->getTitle().'</a></td>
        <td><img src="'.$jani->getImageUrl().'" alt="..." width="1500px" class="img-fluid"></td>
        <td>'.$ani->getGenere().'</td>

        <td>'.$jani->getSynopsis().'</td>
        <td> <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"> <div class="btn-group mr-2" role="group" aria-label="First group"> <a href="take_list.php?action=6&status=4&ID='.$anime['ID'].'"><button type="button" class="btn btn-danger"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
        <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
      </svg></button></a> &nbsp; <a href="take_list.php?action=6&status=2&ID='.$anime['ID'].'"><button type="button" class="btn btn-success"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-all" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path fill-rule="evenodd" d="M8.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14l.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z"/>
      </svg></button></a></div></div> </td>
      </tr>';
      } ?>
    </tbody>
</table>
</div>
<br>
<br>
<?php
} else if( $users->suggestCount() == 0) {
    ?>
<div class="alert alert-success" role="alert">

    Seems like you <b>finished </b> your list! go to the List generator and create another one!
</div>
<div class="card mb-5">
    <img src="./assets/img/success.jpg" class="img-fluid" alt="Responsive image">
</div>
<?php
}
else {
          ?>
<div class="alert alert-danger" role="alert">

    There is something wrong with your suggestion list. Are you sure you created it?
</div>
<div class="card mb-5">
    <img src="./assets/img/error.jpg" class="img-fluid" alt="Responsive image">
</div>
<?php
      }
require __DIR__ . '/assets/html/footer.html';
?>