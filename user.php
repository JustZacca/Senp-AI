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
            <div class="jumbotron jumbotron-fluid">
                <div class="container">
                    <h1 class="display-4">User section</h1>
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
        <dvi class="col-md-6">
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
            <a href="take_list.php?action=1"><button type="button" class="btn btn-primary">Delete list</button></a>
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
</div>

<div class="row">
    <div class="col-md-6">

    </div>
</div>
<div class="row">
    <div class="col-md-6">

    </div>
</div>
</div>

<?php

require __DIR__ . '/assets/html/footer.html';
?>