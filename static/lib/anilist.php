<?php
use Jikan\Jikan;
use Cake\Database\Connection;
use Cake\Database\Driver\Mysql;

class AniList
{
    public $anime;
    public $jikan;
    public $jani;
    public $cache;
    public $driver;
    public $connection;
    public $id;
    public $search_result;
    public function __construct()
    {
        $this->jikan = new Jikan;
        $this->driver = new Mysql([
            'database' => 'senp_AI',
            'username' => 'remote',
            'password' => '11221348Was'
        ]);
        $this->connection = new Connection([
            'driver' => $this->driver
        ]);
    }

    public function query($id)
    {
        $this->id = $id;
        if ($this->isincache()) {
        } else {
            $this->jani = $this->jikan->Anime($id);
            $query = '
            query ($id: Int) { # Define which variables will be used in the query (id)
            Media (idMal: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
                idMal
                title {
                romaji
                english
                }
                genres
                source
                format
                duration
                tags{
                name
                }
                coverImage {
                    large
                }
                trailer {
                    site
                }
            }
            }
            ';
            // Define our query variables and values that will be used in the query request
            $variables = [
                "id" => $id
            ];

            // Make the HTTP Api request
            $http = new GuzzleHttp\Client;
            $response = $http->post('https://graphql.anilist.co', [
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ]
            ]);
            $this->anime = json_decode($response->getBody(), true);
            $this->cacheAnime();
        }
    }

    public function cacheAnime()
    {
        try {
            $this->connection->insert(
                'AnimeCache',
                ['ID_Mal' => $this->getID(), 'Titolo' => $this->getTitle(), 'Tags' => $this->getTags(), 'Genere' => $this->getGenere(), 'IMG' =>$this->getIMG(), 'Source' =>$this->getSource(),'Format' => $this->getFormat(), 'Synopsis' => $this->jani->getSynopsis(), 'Trailer' => $this->getTrailer()]
            );
            return true;
        } catch (Exception $var) {
            return false;
        }
    }

    public function isincache()
    {
        $query = $this->connection->newQuery();
        $query->select(['ID_Mal']);
        $query->where(['ID_Mal' => $this->id]);
        $query->from('AnimeCache');
        return isset($query->execute()->fetchAll()[0][0]);
    }

    public function getTitle()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Titolo']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            if ($this->anime['data']['Media']['title']['english'] == "") {
                return $this->jani->getTitle();
            } else {
                return $this->anime['data']['Media']['title']['english'];
            }
        }
    }

    public function getSynopsis()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Synopsis']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            return $this->jani->getSynopsis();
        }
    }

    public function getID()
    {
        return $this->id;
    }

    public function getGenere()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Genere']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            if (implode(", ", $this->anime['data']['Media']['genres']) == "") {
                return implode(", ", $this->jani->getGenres());
            } else {
                return implode(", ", $this->anime['data']['Media']['genres']);
            }
        }
    }

    public function getSource()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Source']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            if (($out = $this->anime['data']['Media']['source']) == null) {
                return $this->jani->getSource();
            } else {
                return $this->anime['data']['Media']['source'];
            }
        }
    }
    
    public function getFormat()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Format']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            return $this->anime['data']['Media']['format'];
        }
    }
    public function getTags()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Tags']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            return implode(", ", array_map('current', $this->anime['data']['Media']['tags']));
        }
    }

    public function evlTags($id)
    {
        $this->query($id);
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Tags']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            if (count(explode(", ",$query->execute()->fetchAll()[0][0]))) {
                return ($query->execute()->fetchAll()[0][0] === "" ? 0 : count(explode(", ",$query->execute()->fetchAll()[0][0])));
            }
        } else {
            if (count($this->anime['data']['Media']['tags']) >= 1)
            {
                return ($this->anime['data']['Media']['tags'][0] === "" ? 0 : count($this->anime['data']['Media']['tags']));
            }
        }
    }

    public function getIMG()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['IMG']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            return $this->anime['data']['Media']['coverImage']['large'];
        }
    }

    public function is200($id)
    {
        try {
            $query = '
        query ($id: Int) { # Define which variables will be used in the query (id)
        Media (idMal: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
            id
        }
        }
        ';

            // Define our query variables and values that will be used in the query request
            $variables = [
                "id" => $id
            ];

            // Make the HTTP Api request
            $http = new GuzzleHttp\Client;
            $response = $http->post('https://graphql.anilist.co', [
                'json' => [
                    'query' => $query,
                    'variables' => $variables,
                ]
            ]);
            return true;
        } catch (Exception $var) {
            return false;
        }
    }

    public function search($title)
    {
        $query = '
        query ($id: Int, $page: Int, $perPage: Int, $search: String) {
        Page (page: $page, perPage: $perPage) {
            pageInfo {
            total
            currentPage
            lastPage
            hasNextPage
            perPage
            }
            media (id: $id, search: $search) {
            id
            title {
                english
            }
            externalLinks {
            id
            url
            site
            } 
            }
        }
        }
        ';

        $variables = [
            "search" => $title,
            "page" => 1,
            "perPage" => 50
        ];

        $http = new GuzzleHttp\Client;
        $response = $http->post('https://graphql.anilist.co', [
            'json' => [
                'query' => $query,
                'variables' => $variables,
            ]
        ]);
        return $this->search_result = json_decode($response->getBody(), true);
    }

    public function search_count()
    {
        return $this->search_result['data']['Page']['pageInfo']['total'];
    }

    public function error_title($id)
    {
        
        try {
            $this->jani = $this->jikan->Anime($id);
            return $this->jani->getUrl();
        }catch (Exception $var) {
            return false;
        }

    }

    public function show_result()
    {
        $out = '<div class="list-group">';
        foreach($this->search_result['data']['Page']['media'] as $sr)
        {
            
            if(!$sr['title']['english'] == "" && $this->error_title($sr['id']))
            {
                try {
                    $out .=  '<a href='.$this->jani->getUrl().' target="_blank" class="list-group-item list-group-item-action">'.$sr['title']['english'].'</a>';    
                }  catch (Exception $var) {
                    continue;
                }
            }
           
        }
        return $out;
    }

    public function jikanSearch($title)
    {
        return $this->jani = $this->jikan->AnimeSearch($title);
    }

    public function getTrailer()
    {
        if ($this->isincache()) {
            $query = $this->connection->newQuery();
            $query->select(['Trailer']);
            $query->where(['ID_Mal' => $this->id]);
            $query->from('AnimeCache');
            return $query->execute()->fetchAll()[0][0];
        } else {
            return $this->jani->getTrailerUrl();
        }
    }
   
}
?>