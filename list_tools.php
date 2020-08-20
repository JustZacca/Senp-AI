<?php
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$ani = new AniList();
$users->login("Zasser", "11221348Was");
?>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">List tools</h1>
        <p class="lead">
            This section is for managing your list.</p>
    </div>
</div>

<?php
if (file_exists($users->suggestList())) {
    ?>
<div class="alert alert-primary" role="alert">
    Use this section to view the list that Senp-AI has created for you by examining your tastes. Use the button to the
    right of each title to tell the AI ​​if he was right or wrong.
</div>
<br>
<table class="table table-dark">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">Genre</th>
            <th scope="col">Tags</th>
            <th scope="col"></th>
        </tr>
    </thead>
    <tbody>
        <?php
      foreach (json_decode(file_get_contents($users->suggestList()), true) as $anime) {
          $ani->query($anime['ID']);
          echo '<tr>
        <th scope="row">'.$anime['ID'].'</th>
        <td>'.$ani->getTitle().'</td>
        <td>'.$ani->getGenere().'</td>
        <td>'.$ani->getTags().'</td>
      </tr>';
      } ?>
    </tbody>
</table>
<?php
}
else {
  ?>
  <div class="alert alert-danger" role="alert">
    
there is something wrong with your suggestion list. Are you sure you created it?
</div>
<img src="./assets/img/error.jpg" class="img-fluid" alt="Responsive image">
<?php
}
require __DIR__ . '/assets/html/footer.html';
?>