<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\genre as Genre;

class GenreDaos  extends BaseDaos{

    const TABLE_NAME = 'genres';
    private static $instance = null;

    private function __construct(){
        parent::__construct(self::TABLE_NAME, 'Genre');        
    }

    public static function getInstance(){
        if(self::$instance == null){
            self::$instance = new GenreDaos();
        }
        return self::$instance;
    }

    public function getAll(){
        return parent::_getAll();
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function add($genre){
        return parent::_add($genre, true);
    }

    public function getByMovie($idMovie){
        $query ="SELECT g.id_genre,g.name_genre FROM movies_genres mg  INNER JOIN genres g ON g.id_genre = mg.id_genre
        WHERE id_movie = :id_movie";
        
        $params['id_movie'] = $idMovie;        
        $genres = array();     

        try{
            
            $this->connection = Connection::getInstance();
            foreach($this->connection->executeWithAssoc($query, $params) as $genre){
                
                array_push($genres, new Genre($genre['id_genre'], $genre['name_genre']));
    
            }
            return $genres;
        }
        catch(\Exception $ex){
            throw $ex;
        }

    }

    public function update(){
        $genres = $this->updateFromApi();
        foreach($genres as $genre){
            if(!$this->exists($genre->getId())){
                $this->add($genre);
            }
        }
    }


    //api related from here to bottom
    const API_ROOT_URL = "https://api.themoviedb.org/3/";
    const API_DEFAULT_LANG = "es";

    private function updateFromApi($lang = self::API_DEFAULT_LANG){
        $url = self::API_ROOT_URL . "genre/movie/list?api_key=" . MOVIEDB_KEY . "&language=" . $lang;
        $resultRaw = file_get_contents($url);
        $result = json_decode($resultRaw, true);   
        $genres = $result['genres'];

        $resultGenres = array();
        foreach($genres as $genre){
            $resultGenres[] = new Genre($genre['id'], $genre['name']);
        }
        return $resultGenres;
    }

}
?>