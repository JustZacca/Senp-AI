<?php
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;
use Jikan\Jikan;

class users
{
    public $username;
    public $driver;
    public $connection;
    public $UID;
    public $MAL;
    public $filepath;
    public $jikan;
    public $path = "/var/www/html/static/files/";
    public $assets = "/var/www/html/assets/img/";
    public function __construct()
    {
        $this->driver = new Mysql([
            'database' => 'senp_AI',
            'username' => 'remote',
            'password' => '11221348Was'
        ]);
        $this->connection = new Connection([
            'driver' => $this->driver
        ]);
        $this -> anime = new AniList;
    }
    public function createUser($usr, $mal, $pwd)
    {
        $this->setID();
        try {
            $this->connection->insert(
                'Users',
                ['ID' => '0', 'Username' => $usr, 'password' => password_hash($pwd, PASSWORD_DEFAULT), 'MAL_Username' => $mal, 'UID' =>$this->UID]
            );
            return true;
        } catch (Exception $var) {
            return false;
        }
    }

    public function setID()
    {
        $this->UID = uniqid();
    }

    public function setUsername($usr)
    {
        $this->username = $usr;
    }

    public function DBgetID()
    {
        $query = $this->connection->newQuery();
        $query->select(['UID']);
        $query->where(['Username' => $this->username]);
        $query->from('Users');
        $this->UID = $query->execute()->fetchAll()[0][0];
    }
    
    public function getUID()
    {
        return $this->UID;
    }

    public function getMAL()
    {
        return $this->MAL;
    }

    public function getUsername()
    {
        $query = $this->connection->newQuery();
        $query->select(['Username']);
        $query->where(['UID' => $this->UID]);
        $query->from('Users');
        return $query->execute()->fetchAll()[0][0];
    }
    public function DBgetMal()
    {
        $query = $this->connection->newQuery();
        $query->select(['MAL_Username']);
        $query->where(['UID' => $this->UID]);
        $query->from('Users');
        $this->MAL = $query->execute()->fetchAll()[0][0];
    }

    public function login($usr, $pwd)
    {
        try {
            $query = $this->connection->newQuery();
            $query->select(['password']);
            $query->where(['Username' => $usr]);
            $query->from('Users');
            $hash = $query->execute()->fetchAll()[0][0];
            if (password_verify($pwd, $hash)) {
                $this->setUsername($usr);
                $this->DBgetID();
                $this->DBgetMal();

                return true;
            } else {
                return false;
            }
        } catch (Exception $var) {
            return false;
        }
    }
    public function deleteUser()
    {
        try {
            $query = $this->connection->newQuery();
            $query->delete();
            $query->where(['UID' => $this->UID]);
            $query->from('Users');
            $query->execute();
            return true;
        } catch (Exception $var) {
            return false;
        }
    }

    public function getAniList()
    {
        try {
            $link = "https://myanimelist.net/animelist/".$this->getMAL()."/load.json?";
            $filename =  $this->path.$this->UID.".json";
            file_put_contents($filename, fopen($link, 'r'));
            $this->correctList();
        } catch (Exception $var) {
            return false;
        }
    }
    public function listExist()
    {
        return file_exists($this->path.$this->UID.".json");
    }

    public function deletList()
    {
        unlink($this->path.$this->UID.".json");
    }

    public function clistExist()
    {
        return file_exists($this->path.$this->UID."_clean.json");
    }

    public function correctList()
    {
        $filename =  $this->path.$this->UID.".json";
        $ob = file_get_contents($filename);
        $json = json_decode($ob, true);
        $c =0;
        foreach ($json as $anime) {
            try {
                $ani = $this->anime;
                $ani->query($anime['anime_id']);
                $id[$c]['Format'] = $ani->getFormat();
                $id[$c]['Source'] = $ani->getSource();
                $id[$c]['Genere'] = $ani->getGenere();
                $id[$c]['Tag'] = $ani->getTags();
                $id[$c]['Status'] = $this->Status($anime['status']);
                $c++;
            
                $filename =  $this->path.$this->UID."_clean.json";
                file_put_contents($filename, json_encode($id));
            } catch (Exception $var) {
                continue;
            }
        }
    }
    public function ID_List()
    {
        try {
            $AniList =  $this->path.$this->UID.".json";
            $ob = file_get_contents($AniList);
            $json = json_decode($ob, true);
            $id = array();
            foreach ($json as $anime) {
                array_push($id, $anime['anime_id']);
            }
            $filename =  $this->path.$this->UID."_ID.json";
            file_put_contents($filename, json_encode($id));
        } catch (Exception $var) {
            return false;
        }
    }
    public function Already_Seen($ID)
    {
        if (file_exists($this->path.$this->UID."_List.json") && $this->suggestCount() !=0) {
            return in_array($ID, json_decode(file_get_contents($this->path.$this->UID."_ID.json"), true)) | $this->inList($ID);
        } else {
            return in_array($ID, json_decode(file_get_contents($this->path.$this->UID."_ID.json"), true));
        }
    }
    public function suggestCount()
    {
        return count(json_decode(file_get_contents($this->suggestList()), true));
    }
    public function malCount()
    {
        return count(json_decode(file_get_contents($this->userList()), true));
    }

    public function Status($id)
    {
        switch ($id) {
            case 1:
                return "Watching";
                break;
            case 2:
                return "Completed";
                break;
            case 3:
                return "On-Hold";
                break;
            case 4:
                return "Dropped";
                break;
            case 6:
                return "Plan to Watch";
                break;
        }
    }

    public function userList()
    {
        return $this->path.$this->UID."_clean.json";
    }

    public function suggestList()
    {
        return $this->path.$this->UID."_List.json";
    }

    public function randAnime()
    {
        $id = rand(1, 100000);
        if ($this->anime->is200($id)) {
            if (!$this->Already_Seen($id)) {
                if ($this->anime->evlTags($id) > 0) {
                    return $id;
                } else {
                    return $this->randAnime();
                }
            } else {
                return $this->randAnime();
            }
        } else {
            return $this->randAnime();
        }
    }
    
    public function SaveList($list, $f)
    {
        $filename =  $this->path.$this->UID."_List.json";
        if (file_exists($filename) && $f && $this->suggestCount() !=0) {
            $ob = file_get_contents($filename);
            $json = json_decode($ob, true);
            $merge = array_merge($json, $list);
            file_put_contents($filename, json_encode($merge));
        } else {
            file_put_contents($filename, json_encode($list));
        }
    }

    public function deleteFromList($id)
    {
        $filename =  $this->path.$this->UID."_List.json";

        $ob = file_get_contents($filename);
        $json = json_decode($ob, true);
        foreach ($json as $arrid => $ani) {
            if ($ani['ID'] == $id) {
                unset($json[$arrid]);
                $out = array_values($json);
                file_put_contents($filename, json_encode($out));
            }
        }
    }
    public function deleteList()
    {
        unlink($this->path.$this->UID."_List.json");
    }

    public function CorrectAI($status,$anid)
    {
        try {
            $ani = $this->anime;
            $ani->query($anid);
            $id[0]['Format'] = $ani->getFormat();
            $id[0]['Source'] = $ani->getSource();
            $id[0]['Genere'] = $ani->getGenere();
            $id[0]['Tag'] = $ani->getTags();
            $id[0]['Status'] = $this->Status($status);
            $filename =  $this->path.$this->UID."_clean.json";

            $ob = file_get_contents($filename);
            $json = json_decode($ob, true);
            $merge = array_merge($json, $id);
            file_put_contents($filename, json_encode($merge));
            $this->ID_List();
            $this->deleteFromList($anid);
            return true;
            exit();
        } catch (Exception $var) {
            exit();
        }
        exit();
    }
    function getImagesFromDir($status) {
        $images = array();
        switch ($status) {
            case 2:
                $dir = "yes";
            break;
            case 4:
                $dir = "not";
            break;
            case 404:
                $dir = "404";
            break;
        }
        if ( $img_dir = @opendir($this->assets.$dir) ) {
            while ( false !== ($img_file = readdir($img_dir)) ) {
                // checks for gif, jpg, png
                if ( preg_match("/(\.gif|\.jpg|\.png)$/", $img_file) ) {
                    $images[] = $img_file;
                }
            }
            closedir($img_dir);
        }
        return $images;
    }
    
    function getRandomFromArray($ar) {
        $num = array_rand($ar);
        return $ar[$num];
    }

    public function addList($id)
    {
        $list[0]['ID'] = $id;
        $filename =  $this->path.$this->UID."_List.json";
        if (file_exists($filename) && $this->suggestCount() !=0) {
            $ob = file_get_contents($filename);
            $json = json_decode($ob, true);
            $merge = array_merge($json, $list);
            $merge = array_values($merge);
            file_put_contents($filename, json_encode($merge));
        } else {
            file_put_contents($filename, json_encode($list));
        }
    }
    
    public function inList($id)
    {
        $filename =  $this->path.$this->UID."_List.json";

        $ob = file_get_contents($filename);
        $json = json_decode($ob, true);
        foreach ($json as $arrid => $ani) {
            if ($ani['ID'] == $id) {
                return true;
            }
        }
        return false;
    }
    
}
?>