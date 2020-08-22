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
        <li class="breadcrumb-item"><a href="#">AI-Tools</a></li>
        <li class="breadcrumb-item active" aria-current="page">Generate List</li>
    </ol>
</nav>
<div class="jumbotron jumbotron-fluid">
    <div class="container">
        <h1 class="display-4">Generate list</h1>
        <p class="lead">
            Use this utility to generate your suggest list: apply some filters, otherwise the AI will suggest anything,
            even Hentai or strange stuff</p>
    </div>
</div>

<?php
if (file_exists($users->suggestList())) {
    if (!$users->listExist()) {
        ?>
<div class="alert alert-danger" role="alert">



    You can't use this section yet! Go to user and import your MAL, without this <b>Senp-AI</b> you will never know what
    to
    suggest!

</div>
<?php
    } else {
        ?>

<div class="alert alert-primary" role="alert">

    Looks like you already have a list of suggestions. Use this section to regenerate or increase it. <br>MAX 20
    Elements per list.
</div>
<div class="float-right">
    <a href="./list_tools.php">
        <button type="button" class="btn btn-primary pull-right">
            Current list <span class="badge badge-light"><?php echo $users->suggestCount() ?></span>
        </button>
    </a>
</div>
<br>
<br>
<?php
    }
} elseif (!file_exists($users->suggestList())) {
    ?>
<div class="alert alert-warning" role="alert">


    You don't have any list, get ready to generate it! I hopw <b> Senp-AI</b> will do a good work! <br>MAX 20 Elements
    per list.
</div>
<?php
}
 if (file_exists($users->suggestList()) && $users->listExist()) {
     ?>
<div class="accordion" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    Increase
                </button>
            </h2>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <?php
                    if (file_exists($users->suggestList())) {
                        if ($users->suggestCount() == 0) {
                            echo "So, you have ended your list, kinda cool tbh.<br>"; ?>
                <br>
                <form class="form-inline" action="take_list.php" method="post">
                    <input type="hidden" id="action" name="action" value="3">
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Number of elements to add</label>
                    <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref" name="num">
                        <option value=0>Choose...</option>
                        <?php for ($i = 1; $i <= (20 - $users->suggestCount()); $i++) {
                                echo "<option value=".$i.">".$i."</option>";
                            } ?>
                    </select>

                    <button type="submit" class="btn btn-primary my-1">Increase list</button>
                </form>
                <?php
                        } else {
                            echo "I see thath your list have still ".$users->suggestCount()." items (Every list can have a maximum of 20 elements) <br>  Please, tell <b>Senp-AI</b> wich anime you liked, and wich you didn't, becuase if you don't do it, it could suggest them another time"; ?>
                <br>
                <br>
                <form class="form-inline" action="take_list.php" method="post">
                    <input type="hidden" id="action" name="action" value="3">
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Number of elements to add</label>
                    <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref" name="num">
                        <option value=0>Choose...</option>
                        <?php for ($i = 1; $i <= (20 - $users->suggestCount()); $i++) {
                                echo "<option value=".$i.">".$i."</option>";
                            } ?>
                    </select>

                    <button type="submit" class="btn btn-primary my-1">Increase list</button>
                </form>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                    data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    <?php echo (file_exists($users->suggestList())) ? 'Regenerate' : 'Generate'; ?>
                </button>
            </h2>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body">
                <?php
                if ($users->suggestCount() >= 1) {
                    echo "Are you sure about that? You have <b>".$users->suggestCount()." items</b> on your list, you really want to loose them all? <br><br>"; ?>
                <form class="form-inline" action="take_list.php" method="post">
                    <input type="hidden" id="action" name="action" value="4">
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Number of elements </label>
                    <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref" name="num">
                        <option value=0>Choose...</option>
                        <?php for ($i = 1; $i <= 20 ; $i++) {
                        echo "<option value=".$i.">".$i."</option>";
                    } ?>
                    </select>

                    <button type="submit" class="btn btn-warning my-1">Generate list</button>
                </form>
                <?php
                } elseif (!file_exists($users->suggestList())) {
                    echo "First time? Kinda cool tbh. <br>"; ?>
                <form class="form-inline" action="take_list.php" method="post">
                    <input type="hidden" id="action" name="action" value="4">
                    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Number of elements </label>
                    <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref" name="num">
                        <option value=0>Choose...</option>
                        <?php for ($i = 1; $i <= 20 ; $i++) {
                        echo "<option value=".$i.">".$i."</option>";
                    } ?>
                    </select>

                    <button type="submit" class="btn btn-primary my-1">Generate list</button>
                </form>
                <?php
                }
                        }
                    } ?>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header" id="headingThree">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                    data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                    Delete
                </button>
            </h2>
        </div>
        <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
            <div class="card-body">

                Here you can delete items from your list. But I advise you not to do this here, rather go to <a
                    href="./list_tools.php">List Tools</a> and tell <b>Senp-AI</b> that you didn't like the suggestion,
                so next
                time it will suggest you better.<br> <br>
                <a href="take_list.php?action=5"><button type="button" class="btn btn-danger">Delete Anyway</button></a>
            </div>
        </div>
    </div>

</div>

<?php
 } else {
     ?>
<form class="form-inline" action="take_list.php" method="post">
    <input type="hidden" id="action" name="action" value="4">
    <label class="my-1 mr-2" for="inlineFormCustomSelectPref">Number of elements </label>
    <select class="custom-select my-1 mr-sm-2" id="inlineFormCustomSelectPref" name="num">
        <option value=0>Choose...</option>
        <?php for ($i = 1; $i <= 20 ; $i++) {
         echo "<option value=".$i.">".$i."</option>";
     } ?>
    </select>

    <button type="submit" class="btn btn-primary my-1">Generate list</button>
</form>
<?php
 }
require __DIR__ . '/assets/html/footer.html';
?>