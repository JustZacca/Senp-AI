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
   if(!$users->listExist())
{
  ?>
<div class="alert alert-danger" role="alert">


    
You can't use this section yet! Go to user and import your MAL, without this Senp-AI you will never know what to suggest!

</div>
<?php
}
else {
    ?>
  
<div class="alert alert-primary" role="alert">

    Looks like you already have a list of suggestions. Use this section to regenerate or increase it.
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
}
else if (!file_exists($users->suggestList()))
{
?>
<div class="alert alert-warning" role="alert">


    You have no list, get ready to generate it! You will need to add some filters to the generation, in order to make
    the work of Senp-AI easier
</div>
<?php
}
 if(file_exists($users->suggestList()) && $users->listExist()) {
      ?>
<div class="accordion" id="accordionExample">
    <div class="card">
        <div class="card-header" id="headingOne">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse"
                    data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                    <?php echo file_exists($users->suggestList()) ? 'Regenerate' : 'Generate'; ?>
                </button>
            </h2>
        </div>

        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
            <div class="card-body">
                <?php
                    if(file_exists($users->suggestList())) 
                    {
                      if($users->suggestCount() == 0)
                      {
                        echo "So, you have ended your list, kinda cool tbh.";
                      }
                      else 
                      {
                        echo "I see thath your list have still ".$users->suggestCount()." items <br> Please, tell Senp-AI wich anime you liked, and wich you didn't, becuase if you don't do it, it could suggest them another time";
                      }
                    }
                    else {
                      echo "First time? Create some filters, do the AI will to a better prediction." ;
                    }
                ?>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header" id="headingTwo">
            <h2 class="mb-0">
                <button class="btn btn-link btn-block text-left collapsed" type="button" data-toggle="collapse"
                    data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                    Increse
                </button>
            </h2>
        </div>
        <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
            <div class="card-body">
                <?php
                if($users->suggestCount() >= 20)
                {
                  echo "Are you sure about that? You have ".$users->suggestCount()." items on your list, you really need more?";
                }
                else if ($users->suggestCount() <= 20 && $users->suggestCount() > 1 )
                {
                  echo "Tell me how many items you want to add to your list, just give me a number, I'll search something I'm sure you'll like";
                }
                else if(!file_exists($users->suggestList()))
                {
                  echo "Bro, you need to generate your first list before";
                }
                ?>
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
                    href="./list_tools.php">List Tools</a> and tell Senp-AI that you didn't like the suggestion, so next
                time it will suggest you better.
            </div>
        </div>
    </div>

</div>

<?php
}
require __DIR__ . '/assets/html/footer.html';
?>