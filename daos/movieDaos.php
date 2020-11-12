<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\movie as Movie;

class MovieDaos extends BaseDaos{

    const TABLE_NAME = 'movies';

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Movie'); 
        set_time_limit(0);       
    }

    public function getAll(){
        return $this->constructMovies(parent::_getAll());
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function add($movie){
        $query = "INSERT INTO " . self::TABLE_NAME . " (id_movie, title_movie, overview_movie, img_movie, language_movie, releaseDate_movie, duration_movie) values(:id_movie, :title_movie, :overview_movie, :img_movie, :language_movie, :releaseDate_movie, :duration_movie);";

        $params['id_movie'] = $movie->getId();
        $params['title_movie'] = $movie->getTitle();
        $params['overview_movie'] = $movie->getOverview();
        $params['img_movie'] = $movie->getImg();
        $params['language_movie'] = $movie->getLanguage();
        $params['releaseDate_movie'] = $movie->getReleaseDate();
        $params['duration_movie'] = $movie->getDuration();

        $this->connection = Connection::getInstance();
        try{
            return $this->connection->executeNonQuery($query, $params);
        }
        catch(\Exception $ex){
            throw $ex;
        }

    }

    public function getMoviesFiltered($genre, $year, $name, $page = 1, $qty = 16){
        $query ="SELECT m.* from movies m LEFT JOIN movies_genres mg ON m.id_movie = mg.id_movie LEFT JOIN genres g ON mg.id_genre = g.id_genre";
        $params = array();
        $f = false;
        if($genre != "all" | $year != "all" | !empty($name)){
            $query .= " WHERE ";
        }
        
        if($genre != "all"){
            $query .= " g.id_genre = :genre";
            $params['genre'] = $genre;
            $f = true;
            
        }

        if($year != "all"){
            if($f){
                $query .= " and ";
            }
            $params['year'] = $year;
            $query .= " year(releaseDate_movie) = :year";
            $f = true;            
        }

        if(!empty($name)){
            if($f){
                $query .= " and ";
            }
            $params['name'] = $name;
            $query .= " m.title_movie LIKE CONCAT('%',CONCAT(:name, '%'))";
            $f = true;            
        }
        
        $query .= " GROUP BY m.id_movie";

        $offset = ($page - 1) * $qty;
        if($offset < 0) $offset = 1;



        $query .= " LIMIT $qty OFFSET $offset;";
        //echo $query;

  

        $this->connection = Connection::getInstance();
        try{
            $moviesArray = $this->connection->executeWithAssoc($query, $params);
            $movies = array();
            foreach($moviesArray as $movieArray){
                //get genres
                $genreDaos = GenreDaos::getInstance();
                $movie = new Movie($movieArray['id_movie'], $movieArray['title_movie'], $movieArray['overview_movie'], $movieArray['img_movie'], $movieArray['language_movie'], $genreDaos->getByMovie($movieArray['id_movie']), $movieArray['releaseDate_movie'], $movieArray['duration_movie']);
                
                array_push($movies, $movie);
            }
            return $this->constructMovies($movies);
        }
        catch(\Exception $ex){
            throw $ex;
        }

        
    }

    public function getMoviesYear(){
        $query = "SELECT YEAR(releaseDate_movie) as year FROM ". self::TABLE_NAME . " GROUP BY YEAR(releaseDate_movie) order by YEAR(releaseDate_movie) desc";     

        $connection = Connection::getInstance();
        try{
            return $connection->executeWithAssoc($query);
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function constructMovie($movie){

        //api returns movies with 0 duration which messes up all our verifications, this is a quick "fix"
        if($movie->getDuration() == 0){
            $movie->setDuration(60);
        }

        return $movie;
    }

    public function constructMovies($movies){

        $results = array();
        foreach($movies as $movie){

            $movie = $this->constructMovie($movie);


            array_push($results, $movie);
        }


        return $results;
        

    }

    const API_ROOT_URL = "https://api.themoviedb.org/3/";
    const API_IMAGE_URL = "http://image.tmdb.org/t/p/w500";
    const API_DEFAULT_LANG = "es";
    public function update($lang = self::API_DEFAULT_LANG){
        $page = 1;
        do{
            $moviesApi = $this->updateFromApi($page);            
            foreach($moviesApi as $movie){
                if(!$this->exists($movie->getId())){
                    //get duration
                    $url = self::API_ROOT_URL . "movie/{$movie->getId()}}?api_key=" . MOVIEDB_KEY . "&language=$lang";
                    $resultRaw = file_get_contents($url);
                    $runtime = json_decode($resultRaw, true)['runtime'];
                    $movie->setDuration($runtime);
                    $this->add($movie);

                    //instancia de connection
                    $this->connection = Connection::getInstance();
                    try{
                        //llenar movies_genres
                        $genres = $movie->getGenres();
                        foreach($genres as $genre){
                            $query = "INSERT INTO movies_genres (id_movie, id_genre) VALUES(:id_movie,:id_genre);";
                            $params['id_movie'] = $movie->getId();
                            $params['id_genre'] = $genre;
                            //ejecutar query
                            $this->connection->executeNonQuery($query, $params);
                        }
                    }
                    catch(\Exception $ex){
                        throw $ex;
                    }
                }
            }
            $page++;
        }while(!empty($moviesApi));

    }


    private function updateFromApi($page = 1, $lang = self::API_DEFAULT_LANG){
        $url = self::API_ROOT_URL . "movie/now_playing?api_key=" . MOVIEDB_KEY . "&language=" . $lang . "&page=" . $page;
        $resultRaw = file_get_contents($url);
        $result = json_decode($resultRaw, true);   
        $movies = $result['results'];

        $resultMovies = array();

        foreach($movies as $jsonMovie){
            $poster =  (!empty($jsonMovie['poster_path'])) ? self::API_IMAGE_URL . $jsonMovie['poster_path'] : null;
            $movie = new Movie($jsonMovie['id'], $jsonMovie['original_title'], $jsonMovie['overview'], $poster, $jsonMovie['original_language'], $jsonMovie['genre_ids'], $jsonMovie['release_date']);

            $resultMovies[] = $movie;
        }

        return $resultMovies;
    }



    public function getAllMoviesInBillboard(){
        $query = 'SELECT * from movies m
        INNER JOIN shows s ON s.idMovie_show = m.id_movie
        WHERE s.datetime_show > now()
        GROUP BY m.id_movie;';

        $this->connection = Connection::getInstance();
        try{
            $resultSet = $this->connection->execute($query);
            $movies = array();
            foreach($resultSet as $movieArray){
                //get genres
                $genreDaos = GenreDaos::getInstance();
                $movie = new Movie($movieArray['id_movie'], 
                                    $movieArray['title_movie'],
                                    $movieArray['overview_movie'],
                                    $movieArray['img_movie'],
                                    $movieArray['language_movie'],
                                    $genreDaos->getByMovie($movieArray['id_movie']),
                                    $movieArray['releaseDate_movie'],
                                    $movieArray['duration_movie']
                                );
                
                array_push($movies, $movie);
            }
            return $this->constructMovies($movies);
        }
        catch(\Exception $ex){
            throw $ex;
        }

    }


    
    public function getAllMoviesInBillboardTest($genre = null, $date = null){      
        $query = "SELECT * from movies m
        INNER JOIN shows s ON s.idMovie_show = m.id_movie
        LEFT JOIN movies_genres mg ON m.id_movie = mg.id_movie
        LEFT JOIN genres g ON mg.id_genre = g.id_genre
        WHERE s.datetime_show > now()" . 
        (($genre)?" AND g.id_genre = :genre":"") . 
        //if date is not null add it to query
        (($date)?" AND DATE(s.datetime_show) = DATE(:date)":"") .
        " GROUP BY m.id_movie;";

        //echo $query;

        $params = array();
        if($genre) $params['genre'] = $genre;
        if($date) $params['date'] = $date;

 

        
        $this->connection = Connection::getInstance();
        try{
            $resultSet = $this->connection->execute($query, $params);
            $movies = array();
            foreach($resultSet as $movieArray){
                //get genres
                $genreDaos = GenreDaos::getInstance();
                $movie = new Movie($movieArray['id_movie'], 
                                    $movieArray['title_movie'],
                                    $movieArray['overview_movie'],
                                    $movieArray['img_movie'],
                                    $movieArray['language_movie'],
                                    $genreDaos->getByMovie($movieArray['id_movie']),
                                    $movieArray['releaseDate_movie'],
                                    $movieArray['duration_movie']
                                );
                
                array_push($movies, $movie);
            }
            return $this->constructMovies($movies);

        }
        catch(\Exception $ex){
            throw $ex;
        }

    }

}
?>