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

    public function getUsername($UID)
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
                echo $anime['anime_id']," ";
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
        if (file_exists($this->path.$this->UID."_List.json")) {
            return in_array($ID, array_merge(json_decode(file_get_contents($this->path.$this->UID."_ID.json"), true), json_decode(file_get_contents($this->path.$this->UID."_List.json"), true)));
        } else {
            return in_array($ID, json_decode(file_get_contents($this->path.$this->UID."_ID.json"), true));
        }
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

    public function randAnime()
    {
        $id = rand(1, 100000);
        if ($this->anime->is200($id)) {
            if ($this->Already_Seen($id)) {
                return $this->randAnime();
            } else {
                return $id;
            }
        } else {
            return $this->randAnime();
        }
    }
    
    public function SaveList($list)
    {
        $filename =  $this->path.$this->UID."_List.json";
        if (file_exists($filename)) {
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
                exit();
            }
        }
    }
}
