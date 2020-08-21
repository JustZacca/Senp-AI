<?php
use Jikan\Jikan;

class AniList
{
    public $anime;
    public $jikan;
    public $jani;

    public function query($id)
    {
        $this->jikan = new Jikan;
        $this->jani = $this->jikan->Anime($id);
        $query = '
            query ($id: Int) { # Define which variables will be used in the query (id)
            Media (idMal: $id, type: ANIME) { # Insert our variables into the query arguments (id) (type: ANIME is hard-coded in the query)
                id
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
        return $this->anime = json_decode($response->getBody(), true);
    }

    public function getTitle()
    {
        if ($this->anime['data']['Media']['title']['english'] == "") {
            return $this->jani->getTitle();
        } else {
            return $this->anime['data']['Media']['title']['english'];
        }
    }

    public function getGenere()
    {
        return implode(", ", $this->anime['data']['Media']['genres']);

        if (implode(", ", $this->anime['data']['Media']['genres']) == "") {
            return implode(", ", $this->jani->getGenres());
        } else {
            return implode(", ", $this->anime['data']['Media']['genres']);
        }
    }

    public function getSource()
    {
        if (($out = $this->anime['data']['Media']['source']) == null) {
            return "null";
        } else {
            return $this->anime['data']['Media']['source'];
        }
    }
    
    public function getFormat()
    {
        return $this->anime['data']['Media']['format'];
    }
    public function getTags()
    {
        return implode(", ", array_map('current', $this->anime['data']['Media']['tags']));
    }

    public function evlTags($id)
    {
        $this->query($id);
        return (count($this->anime['data']['Media']['tags']) > 1) ? true : false;
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
}
