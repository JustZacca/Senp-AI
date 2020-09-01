<?php
//DO NOT DISPLAY ERRORS TO USER
ini_set("display_errors", 0);
ini_set("log_errors", 1);

//Define where do you want the log to go, syslog or a file of your liking with
ini_set("error_log", dirname(__FILE__).'/php_errors.log');

register_shutdown_function(function(){
    $last_error = error_get_last();
    if ( !empty($last_error) && 
         $last_error['type'] & (E_ERROR | E_COMPILE_ERROR | E_PARSE | E_CORE_ERROR | E_USER_ERROR)
       )
    {
       require_once(dirname(__FILE__).'/505.php');
       exit(1);
    }
});

require __DIR__ . '/assets/html/head.html';
set_time_limit(360);
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/static/Autoloader.php';
require __DIR__ . '/assets/html/menu.html';
$users = new Users();
$ani = new AniList();
$users->login("Zasser", "11221348Was");
$AI = new AI($users);


$id = $_GET['ID'] != "" ? $_GET['ID'] : $users->randAnime();
$ani->query($id);
$rs = $AI->SingleMatch($id);

?>
<script>
$('#example').tooltip({
    boundary: 'window'
})
</script>
<div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="jumbotron" style="padding-top:1em; padding-bottom:1em;">
                    <h4><?php echo $_GET['ID'] != "" ? "Anime Page" : "Random"?></h4>
                    <p>
                        Use this section to improve the prediction results. Here you can tell Senp-AI if it gave a wrong
                        prediction or add the item to your list and evaluate later.</p>
                </div>
                <?php
                if ($_GET["status"] == 2 | $_GET["status"] == 4) {
                    ?><div class="alert alert-success" role="alert">
                   Now Senp-AI knows your taste better!
                </div>
                <?php
                } else if($_GET["status"] == 9) {
                    ?>
                <div class="alert alert-info" role="alert">
                    Element added to your list
                </div>
                <?php

                }
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 col-xl-3">
                <div class="card text-white" style="height:100%;">
                    <div class="card-body">
                        <h5 class="card-title" style="font-size:20px;"><?php echo $ani->getTitle(); ?></h5>
                        <img class="card-img-top" src="<?php echo $ani->getIMG() ?>" alt="Card image cap">
                        <h6 class="text-muted card-subtitle mb-2"></h6>
                        <p class="card-text"><?php echo $ani->getGenere();?></p>
                        <?php
                        if ($rs == 'Completed' | $rs == 'Watching') {
                            echo '<div class="alert alert-success mx-auto " role="alert">
                          '.$rs.'
                        </div>';
                        } else {
                            echo '<div class="alert alert-warning mx-auto " role="alert">
                          '.$rs.'
                        </div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-7">
                <?php
                if ($ani->getTrailer()!="") {
                    ?>
                <div class="video-container">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item"
                            src="<?php echo str_replace("autoplay=1", "autoplay=0", $ani->getTrailer()) ?>"
                            allowfullscreen></iframe>
                    </div>
                </div>
                <?php
                } else {
                    echo '<img src="./assets/img/404/'.$users->getRandomFromArray($users->getImagesFromDir(404)).'"class="img-fluid" style="padding-bottom:15px;" alt="...">';
                }
            
                ?>
                <div class="btn-group" style="  margin-top: 25;"></div>
                <div class="btn-toolbar">
                    <div class="btn-group">
                        <a href="take_list.php?action=7&status=2&ID=<?php echo $id ?>"><button class="btn btn-success"
                                type="button" ata-toggle="tooltip" data-placement="top" title="Completed"><svg
                                    width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-check-all"
                                    fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M8.97 4.97a.75.75 0 0 1 1.071 1.05l-3.992 4.99a.75.75 0 0 1-1.08.02L2.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093L8.95 4.992a.252.252 0 0 1 .02-.022zm-.92 5.14l.92.92a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 1 0-1.091-1.028L9.477 9.417l-.485-.486-.943 1.179z" />
                                </svg></button></a>
                        &nbsp;<a href="take_list.php?action=8&ID=<?php echo $id ?>"><button class="btn btn-primary"
                                type="button" ata-toggle="tooltip" data-placement="top" title="Add to list"
                                <?php echo $users->Already_Seen($id) ? "disabled":"" ?>><svg width="1em" height="1em"
                                    viewBox="0 0 16 16" class="bi bi-bookmark-plus-fill" fill="currentColor"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd"
                                        d="M4 0a2 2 0 0 0-2 2v13.5a.5.5 0 0 0 .74.439L8 13.069l5.26 2.87A.5.5 0 0 0 14 15.5V2a2 2 0 0 0-2-2H4zm4.5 4.5a.5.5 0 0 0-1 0V6H6a.5.5 0 0 0 0 1h1.5v1.5a.5.5 0 0 0 1 0V7H10a.5.5 0 0 0 0-1H8.5V4.5z" />
                                </svg></button></a>
                        &nbsp;
                        <a href="take_list.php?action=7&status=4&ID=<?php echo $id ?>"><button
                                class="btn btn-danger pull-right" type="button" ata-toggle="tooltip"
                                data-placement="top" title="Dropped"><svg width="1em" height="1em" viewBox="0 0 16 16"
                                    class="bi bi-trash" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z" />
                                    <path fill-rule="evenodd"
                                        d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4L4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z" />
                                </svg></button></a>
                    </div>
                </div>
            </div>
            <div class="col-xl-2">
                <div class="card text-white" style="height:100%;">
                    <div class="card-body">
                        <h4 class="card-title" style="font-size:20px;">Stats</h4>
                        <p><?php echo "<b>Soruce:</b> ".$ani->getSource()."<br><br> <b>Format</b>: ".$ani->getFormat()."<br><br> <b>Tags</b>: ".$ani->getTags()."</p>"; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="padding-top:20px">
            <div class="col-md-12 ">
                <div class='overflow-auto'>
                    <div class="jumbotron" style="padding-top:1em; padding-bottom:1em;">
                        <h4>Synopsis</h4>
                        <p>
                            <?php echo $ani->getSynopsis() ?>
                    </div>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
</div>


<?php
require __DIR__ . '/assets/html/footer.html';
?>