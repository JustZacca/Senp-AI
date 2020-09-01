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
$users->login("Zasser", "11221348Was");
?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="jumbotron" style="padding-top:1em; padding-bottom:1em;">
                <div class="container">
                    <h1 class="display-4">Senp-AI</h1>
                    <p class="lead">
                        Artificial Senpai
                    </p>
                </div>

                <div class="text-center">
                    <img src="./assets/img/cowboy-bebop.gif" class="img-fluid" alt="...">
                    <br>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <dl class="row">
                <dt class="col-sm-3">Why?</dt>
                <dd class="col-sm-9">Finding New Series is difficult, you must first find the title, then inform
                    yourself, look for a trailer and ask for advice from strangers on forums or friends with completely
                    different tastes from yours. But what if there was another way to find new series? An AI that, based
                    on your tastes, randomly selects a series of anime that he thinks you might like and generates a
                    list for you? Maybe only I felt the need for all this, but if you also need it, so be it. Oh, it can
                    generate hentai lists too</dd>

                <dt class="col-sm-3">How?</dt>
                <dd class="col-sm-9">
                    <p>
                        Let's start from the beginning: Senp-AI does not predict the future, it only makes predictions
                        that are as reliable as the weather forecast. Based on the data from your My Anime List, Senp-AI
                        tries to create a pattern of what you like and uses this pattern to predict if you like a given
                        item or not. Often it gets it right, but many other times it fails, so you should train it in
                        the appropriate section or by evaluating the predictions.</p>
                </dd>

                <dt class="col-sm-3">Does it really work?</dt>
                <dd class="col-sm-9">Roughly, it's kind of like magic: it works if you believe it. Quite often it gets
                    it right, but of course that depends on how many anime you have on your list. If you have 10 and
                    they are all marked "COMPLETED" then Senp-AI doesn't have enough data. We need data that are as
                    varied and representative as possible. But don't worry, use Senp-AI and over time it will be able to
                    give you better predictions. </dd>

                <dt class="col-sm-3 text-truncate">Will the AI improve?</dt>
                <dd class="col-sm-9">
                    Being machine learning, Senp-AI improves the more you use it.</dd>

                <dt class="col-sm-3 text-truncate">But really, Why?</dt>
                <dd class="col-sm-9">
                    If you insist, I'll tell you: I like money.</dd>
            </dl>
        </div>
    </div>
</div>
<?php
require __DIR__ . '/assets/html/footer.html';
?>