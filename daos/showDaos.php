<?php
namespace daos;
use daos\BaseDaos as BaseDaos;
use models\Show as Show;
use models\Movie as Movie;
use models\Room as Room;
use models\Cinema as Cinema;

class ShowDaos extends BaseDaos{

    const TABLE_NAME = "shows";
    const SHOW_INTERVAL = 15;
    private $genreDaos;

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Show');        
        $this->genreDaos = GenreDaos::getInstance();
    }

    public function getAll(){
        //var_dump(parent::_getAll());

        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.* FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        ORDER BY s.datetime_show";

        
        try{
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query);

            $results = array();
    
            foreach ($resultSet as $show){
                $results[] = $this->mapShow($show);                
            }
    
            return $results;
        }
        catch(\Exception $ex){
            throw $ex;
        }
        
    }

    public function mapShow($showArray){
        $object = new show(
            new Movie(
                $showArray['id_movie'],
                $showArray['title_movie'], 
                $showArray['overview_movie'], 
                $showArray['img_movie'], 
                $showArray['language_movie'],                                    
                $this->genreDaos->getByMovie($showArray['id_movie']),                                    
                $showArray['releaseDate_movie'],
                $showArray['duration_movie']),
            new Room(
                $showArray['name_room'],
                $showArray['price_room'],
                $showArray['capacity_room'],
                new Cinema(
                    $showArray['name_cinema'],
                    $showArray['address_cinema'],
                    $showArray['city_cinema'],
                    $showArray['zip_cinema'],
                    $showArray['province_cinema']
                )
            ),
            $showArray['datetime_show']
            );
        $object->getRoom()->setId($showArray['id_room']);
        $object->getRoom()->getCinema()->setId($showArray['id_cinema']);
        $object->setId($showArray['id_show']);
        return $object;

    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.* FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE s.id_show = :id_show";

        
        $parameters['id_show'] = $id; 
        try{
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters);

            if($resultSet == null){
                return null;
            }

            $resultSet = $resultSet[0];
            return $this->mapShow($resultSet);
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function add($show){

        $query = "INSERT INTO " . self::TABLE_NAME . " (idMovie_show, datetime_show, idRoom_show)
                                                        values(:idMovie_show, :datetime_show, :idRoom_show);";

        $params['idMovie_show'] = $show->getMovie()->getId();
        $params['idRoom_show'] = $show->getRoom()->getId();
        $params['datetime_show'] = $show->getDatetime();

        
        try{
            $this->connection = Connection::getInstance();
            return $this->connection->executeNonQuery($query, $params);
        }
        catch(\Exception $ex){
            throw $ex;
        }

    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

    public function modify($show){

        $query = "UPDATE " . self::TABLE_NAME . " SET idMovie_show = :idMovie_show,
        datetime_show = :datetime_show,
        idRoom_show = :idRoom_show
        WHERE id_show = :id_show";

        $parameters['idMovie_show'] = $show->getMovie()->getId();
        $parameters['datetime_show'] = $show->getDatetime();
        $parameters['idRoom_show'] = $show->getRoom()->getId();
        $parameters['id_show'] = $show->getId();

        try{
            $this->connection = Connection::getInstance();
            $this->connection->ExecuteNonQuery($query, $parameters);

        } catch(\Exception $e) {
            throw $e;
        }
    }

    public function verifyShowDay($show){
        
        $query = 'SELECT c.id_cinema, s.idMovie_show, s.datetime_show, s.idRoom_show from ' . self::TABLE_NAME . ' s
        INNER JOIN movies m ON m.id_movie = s.idMovie_show
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON c.id_cinema = r.idCinema_room
        WHERE DATE(s.datetime_show) = DATE(:datetime_show) AND s.idMovie_show = :id_movie'; //AND r.idCinema_room != :id_cinema AND s.idRoom_show = :id_room;


        $parameters['datetime_show'] = $show->getDatetime();
        $parameters['id_movie'] = $show->getMovie()->getId();
        

        try{
            $connection = Connection::getInstance();
            $resultSet = $connection->execute($query,$parameters);
    
            return $resultSet;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }
     
    public function verifySameRoom($show, $idRoom){

        $query = "SELECT * FROM shows s
        WHERE idRoom_show = :idRoom";

        $params['idRoom'] = $idRoom;

        try{
            $connection = Connection::getInstance(); 
            $resultSet = $connection->execute($query,$params);
            echo "<pre>" ;
            var_dump($resultSet);
            echo "</pre>" ;
            return $resultSet;
        }catch(\Exception $ex){
            throw $ex;
        }


    }

    public function verifyShowDatetimeOverlap($_show){
        $query = "SELECT s.*, m.*, r.*, c.* FROM shows s
        inner join movies m on m.id_movie = s.idMovie_show
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON c.id_cinema = r.idCinema_room
        WHERE (DATE(s.datetime_show) = DATE(DATE_SUB(:datetime_show, INTERVAL 1 DAY))
        OR DATE(s.datetime_show) = DATE(:datetime_show)
        OR DATE(s.datetime_show) = DATE(DATE_ADD(:datetime_show, INTERVAL 1 DAY)))
        AND (s.idRoom_show = :idRoom_show)";

        $parameters['datetime_show'] = $_show->getDatetime();
        $parameters['idRoom_show'] = $_show->getRoom()->getId();

        try{        
            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query,$parameters);

            $results = array();

            foreach ($resultSet as $show){
                $results[] = $this->mapShow($show);
            }
            return $results;


        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function getByIdMovie($id){
        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.* FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE m.id_movie = :id_movie
        ORDER BY s.datetime_show";

        $parameters['id_movie'] = $id;

        try{       

            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters);

            $results = array();

            foreach ($resultSet as $show){
                $results[] = $this->mapShow($show);
            }

            return $results;

        }catch(\Exception $ex){
            throw $ex;
        }
    }

    public function getByIdMovieFuture($id){
        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.* FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE m.id_movie = :id_movie
        AND s.datetime_show > now();
        ORDER BY s.datetime_show";

        $parameters['id_movie'] = $id;

        try{       

            $connection = Connection::getInstance();
            $resultSet = $connection->executeWithAssoc($query, $parameters);

            $results = array();

            foreach ($resultSet as $show){
                $results[] = $this->mapShow($show);
            }

            return $results;

        }catch(\Exception $ex){
            throw $ex;
        }
    }


}
?>