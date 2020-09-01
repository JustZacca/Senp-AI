<?php
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$users->login("Zasser", "11221348Was");
?>
<div class=container>
    <div class="row">
        <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item active" aria-current="page">Users</li>
                </ol>
            </nav>
            <div class="jumbotron jumbotron-fluid" style="padding-top:1em; padding-bottom:1em;">
                <div class="container">
                    <h4>User section</h4>
                    <p class="lead">
                        This server section for managing your profile and main list.</p>
                </div>
                <div class="text-center">
                    <img src="./assets/img/user.gif" class="img-fluid" alt="...">
                    <br>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <p> Hi <?php  echo $users->getUsername()  ?> <br>Check that everything is ready:
                <?php
  if ($users->listExist()) {
      if (!$users->clistExist()) {
          ?>
            <div class="alert alert-warning" role="alert">

                Your MAL is ready, but for som problem has not been cleaned
            </div>
            <a href="take_list.php?action=2"><button type="button" class="btn btn-primary">Prepare list</button></a>
            <?php
      } else {
          ?>
            <div class="alert alert-success" role="alert">

                Your MAL is ready
            </div>
            <a href="take_list.php?action=1"><button type="button" class="btn btn-danger"><svg width="1em" height="1em"
                        viewBox="0 0 16 16" class="bi bi-person-lines-fill" fill="currentColor"
                        xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd"
                            d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6zm7 1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm0-3a.5.5 0 0 1 .5-.5h4a.5.5 0 0 1 0 1h-4a.5.5 0 0 1-.5-.5zm2 9a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 0 1h-2a.5.5 0 0 1-.5-.5z" />
                    </svg> Delete list</button></a>
            <?php
      }
  } else {
      ?>
            <div class="alert alert-danger" role="alert">
                Your MAL is not on our servers, press the button (this could take a while)
            </div>
            <a href="take_list.php?action=0"><button type="button" class="btn btn-danger">Generate list</button></a>
            <?php
  }
?>
</div>
    <div class="col-md-6">
        <?php
         if ($users->listExist()) {
            if ($users->clistExist()) {
                
        echo "<p> Anime on your <b>MAL:</b> ".$users->malCount().
                  "<br>Anime on your <a href='list_tools.php'>list</a>: ".$users->suggestCount()."";
            }
        }
        ?>
    </div>
</div>
<div class="row">
    <div class="col-md-6">

    </div>
</div>
</div>
</div>
<?php

require __DIR__ . '/assets/html/footer.html';
?>