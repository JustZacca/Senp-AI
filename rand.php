<?php
require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$users = new Users();
$ani = new AniList();
$users->login("Zasser", "11221348Was");
$AI = new AI($users);
$id = $users->randAnime();
$ani->query($id);
$rs = $AI->SingleMatch($id);

?>
<div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="#"><span>Home</span></a></li>
                    <li class="breadcrumb-item"><a href="#"><span>Library</span></a></li>
                    <li class="breadcrumb-item"><a href="#"><span>Data</span></a></li>
                </ol>
                <div class="jumbotron">
                    <h1>Heading text</h1>
                    <p>Nullam id dolor id nibh ultricies vehicula ut id elit. Cras justo odio, dapibus ac facilisis in,
                        egestas eget quam.</p>
                    <p><a class="btn btn-primary">Learn more</a></p>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card text-white bg-dark" style="height:100%;">
                    <div class="card-body">
                        <h4 class="card-title"><?php echo $ani->getTitle(); ?></h4>
                        <img class="card-img-top" src="<?php echo $ani->getIMG() ?>" alt="Card image cap">
                        <h6 class="text-muted card-subtitle mb-2"></h6>
                        <p class="card-text"><?php echo $ani->getTags();?></p>
                        <?php 
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
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-7">
                <div class="video-container">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item"
                            src="<?php echo $ani->getTrailer()!="" ? str_replace("autoplay=1", "autoplay=0", $ani->test()) : "./assets/img/404/".$users->getRandomFromArray($users->getImagesFromDir(404)) ?>"
                            allowfullscreen></iframe>
                    </div>
                </div>
                <div class="btn-group" style="  margin-top: 25;"></div>
                <div class="btn-toolbar">
                    <div class="btn-group"><button class="btn btn-primary" type="button">Button 1</button>
                        &nbsp;<button class="btn btn-primary" type="button">Button 2</button></div>
                </div>
            </div>
            <div class="col-xl-2" style="height: 32em;">
                <div class="card text-white bg-dark">
                    <div class="card-body" style="height: 32em;">
                        <h4 class="card-title">Stats</h4>
                        <div role="alert" class="alert alert-success"><span><strong>Alert</strong> text.</span></div>
                        <div role="alert" class="alert alert-success"><span><strong>Alert</strong> text.</span></div>
                        <div role="alert" class="alert alert-success"><span><strong>Alert</strong> text.</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require __DIR__ . '/assets/html/footer.html';
?>