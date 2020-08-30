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
<div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="#">AI-Tools</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Single Check</li>
                    </ol>
                </nav>
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
                            <input type="text" class="form-control" name="title" id="title" placeholder="Anime Title">
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-primary"><svg width="1em" height="1em"
                                    viewBox="0 0 16 16" class="bi bi-search" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M10.442 10.442a1 1 0 0 1 1.415 0l3.85 3.85a1 1 0 0 1-1.414 1.415l-3.85-3.85a1 1 0 0 1 0-1.415z" />
                                    <path fill-rule="evenodd"
                                        d="M6.5 12a5.5 5.5 0 1 0 0-11 5.5 5.5 0 0 0 0 11zM13 6.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0z" />
                                </svg></button>
                        </div>
                    </div>
                </form>
                <br>
            </div>
        </div>
        <div class="row">
            <?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $res = $ani->jikanSearch($_POST['title'])->getResults(); ?>
    <div class="col-md-12">
            <table class="table table-bordered table-dark table-resposnive">
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
      <td><a href="single_check.php?ID='.$res->getMalId().'">'.$res->getTitle().'</a></td>
      <td><img class ="card-img-top-ani" src="'.$res->getImageUrl().'" alt="..." width="150px" ></td>
      <td>'.$res->getSynopsis().'</td>
    </tr>';
    } ?>
                </tbody>
            </table>
            </div>
            <?php
} else if(empty($_GET['ID'])) {
        ?> <div class="col-md-12">
            <div class="text-center">
                <img src="./assets/img/waiting.gif" class="rounded mx-auto d-block" alt="...">
                <br>
                <br>
            </div>
        </div>
        </div>

        <?php
    }
if ($_SERVER['REQUEST_METHOD'] == 'GET' && !empty($_GET['ID'])) {
    //check if the anime searched is ok for the AI
    $AI = new AI($users);
    try 
    {
      $ani->query($_GET['ID']); } 
      catch (Exception $var) {
        ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <strong>
                This time, the result you chose was not found, who knows why, really, I don't know</strong> <br>
            <div class="text-center">
                <img src="<?php echo "./assets/img/404/".$users->getRandomFromArray($users->getImagesFromDir(404))?>">
                <br>
            </div>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div> <?php
        
    }
    $rs = $AI->SingleMatch($_GET['ID']);
    echo ' <div class="col-xl-3"><div class="card card-ani text-white bg-dark mb-3" >
    <div class="card-header">'.$ani->getTitle().'</div>
    <img class="card-img-top " src="'.$ani->getIMG().'" alt="Card image cap">
    <div class="card-body">';

    if($rs == 'Completed' | $rs == 'Watching')
    {
      echo '<div class="alert alert-success mx-auto " role="alert">
      '.$rs.'
    </div>';

    }
    else{
      echo '<div class="alert alert-warning mx-auto " role="alert">
      '.$rs.'
    </div>';
    }
      echo '
      <p class="card-text mt-auto">
      If the result is not correct, please use the keys below to correct Senp-AI </p>
    </div>
    <div class="card-footer bg-transparent border-light mx-auto">
    <div class="btn-toolbar" role="toolbar" aria-label="Toolbar with button groups"> <div class="btn-group mr-2" role="group" aria-label="First group"> <a href="take_list.php?action=6&status=4&ID='.$_GET['ID'].'"><button type="button" class="btn btn-danger"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/>
    <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/>
  </svg></button></a> &nbsp; <a href="take_list.php?action=6&status=2&ID='.$_GET['ID'].'"><button type="button" class="btn btn-success"><svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-all" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
    <path fill-rule="evenodd" d="M8.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14l.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z"/>
  </svg></button></a></div></div>
    </div>
    </div>
    </div>
    <div class="col-md-6 col-xl-9 offset-xl-0">
  ';

  echo '';
  if($ani->getTrailer() != "")
  {
    echo '<div class="embed-responsive embed-responsive-16by9 float-right">
    <iframe class="embed-responsive-item" src="'.str_replace("autoplay=1", "autoplay=0", $ani->getTrailer()).'" allowfullscreen></iframe>
    </div>';
  }
  else
  {
    echo'<figure class="figure">
    <img class="im404" src="./assets/img/404/'.$users->getRandomFromArray($users->getImagesFromDir(404)).'" alt="...">
    <figcaption class="figure-caption">'; ?> if i can't find the trailers, I put stupid pictures.<br> Graphically I
        don't know how to do better, if you are a front-end do it for me</figcaption><?php
  echo '</figure>';
  }

}

?>
        <?php
require __DIR__ . '/assets/html/footer.html';
?>