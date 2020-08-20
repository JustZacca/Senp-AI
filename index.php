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
        <li class="breadcrumb-item active" aria-current="page">Home</li>
    </ol>
</nav>
<h1 class="display-4">Senp-AI</h1>
<p class="lead">
    Artificial Senpai
</p>
<p class="text-justify">
    Senp-AI is a web app that uses machine learning to understand your anime taste and suggest some titles. It's based
    upon MyAnimeList and AniList databases and uses your MAL as a dataset for the ML algorithm.
</p>
<dl class="row">
    <dt class="col-sm-3">Why?</dt>
    <dd class="col-sm-9">I'm just bored</dd>

    <dt class="col-sm-3">How?</dt>
    <dd class="col-sm-9">
        <p>Using your My Anime List I create a dataset. This dataset is without the anime name and your ratings but
            keeps the status(Dropped or Completed) and genre, tags, studio and other stuff. The Senp-AI uses those data
            determinate what you like and what you don't like and then compares it with the anime other anime and does a
            prediction.</p>
    </dd>

    <dt class="col-sm-3">Does it really work?</dt>
    <dd class="col-sm-9">It depends. The AI can't exactly predict the future, it can just guess. Every guess depends on
        your list, the quantity of the data and if the data is accurate. </dd>

    <dt class="col-sm-3 text-truncate">Will the AI improve?</dt>
    <dd class="col-sm-9">
        This AI can obviously improve, but it will not do it by itself, the ML works through assisted teaching, the more
        you use it the more it improves, in the appropriate section say to the AI ​​when its prediction is wrong, so in
        the future it will avoid making mistakes.</dd>

        <dt class="col-sm-3 text-truncate">But really, Why?</dt>
    <dd class="col-sm-9">
    If you insist, I'll tell you: I like money.</dd>
</dl>
<?php
require __DIR__ . '/assets/html/footer.html';
?>