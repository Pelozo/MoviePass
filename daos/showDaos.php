<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\show as Show;
use models\movie as Movie;
use models\room as Room;

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

        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.name_cinema FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        ORDER BY s.datetime_show";

        $connection = Connection::getInstance();
        try{
            $resultSet = $connection->executeWithAssoc($query);
    
    
            $results = array();
    
            foreach ($resultSet as $show){
                $object = new show(
                                    new Movie(
                                        $show['id_movie'],
                                        $show['title_movie'], 
                                        $show['overview_movie'], 
                                        $show['img_movie'], 
                                        $show['language_movie'],                                    
                                        $this->genreDaos->getByMovie($show['id_movie']),                                    
                                        $show['releaseDate_movie'],
                                        $show['duration_movie']),
                                    new Room(
                                        $show['name_room'],
                                        $show['price_room'],
                                        $show['capacity_room'],
                                        $show['idCinema_room']
                                    ),
                                    $show['datetime_show']
                                );
                $object->getRoom()->setId($show['id_room']);
                $object->setId($show['id_show']);
                $results['shows'][] = $object;
    
                $results['cinemas'][] = $show['name_cinema'];
            }
    
            return $results;
        }
        catch(\Exception $ex){
            throw $ex;
        }
        
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.name_cinema FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE s.id_show = :id_show";

        $connection = Connection::getInstance();
        $parameters['id_show'] = $id; 
        try{
            $resultSet = $connection->executeWithAssoc($query, $parameters)[0];
    
            $object = new show(
                                new Movie(
                                    $resultSet['id_movie'],
                                    $resultSet['title_movie'], 
                                    $resultSet['overview_movie'], 
                                    $resultSet['img_movie'], 
                                    $resultSet['language_movie'],                                    
                                    $this->genreDaos->getByMovie($resultSet['id_movie']),                                    
                                    $resultSet['releaseDate_movie'],
                                    $resultSet['duration_movie']),
                                new Room(
                                    $resultSet['name_room'],
                                    $resultSet['price_room'],
                                    $resultSet['capacity_room'],
                                    $resultSet['idCinema_room']
                                ),
                                $resultSet['datetime_show']
                            );
            $object->getRoom()->setId($resultSet['id_room']);
            $object->setId($resultSet['id_show']);
    
            return $object;
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

        $this->connection = Connection::getInstance();
        try{
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
        try{
            $query = "UPDATE " . self::TABLE_NAME . " SET idMovie_show = :idMovie_show,
            datetime_show = :datetime_show,
            idRoom_show = :idRoom_show
            WHERE id_show = :id_show";
    
            $parameters['idMovie_show'] = $show->getMovie()->getId();
            $parameters['datetime_show'] = $show->getDatetime();
            $parameters['idRoom_show'] = $show->getRoom()->getId();
            $parameters['id_show'] = $show->getId();

            $this->connection = Connection::getInstance();
            $this->connection->ExecuteNonQuery($query, $parameters);

        } catch(Exception $e) {
            throw $e;
        }
    }

    public function verifyShowDay($show, $idCinema){
        
        $query = 'SELECT c.id_cinema, s.idMovie_show, s.datetime_show from ' . self::TABLE_NAME . ' s
        INNER JOIN movies m ON m.id_movie = s.idMovie_show
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON c.id_cinema = r.idCinema_room
        WHERE DAY(s.datetime_show) = DAY(:datetime_show) AND s.idMovie_show = :id_movie AND r.idCinema_room != :id_cinema;';
        

        $parameters['datetime_show'] = $show->getDatetime();
        $parameters['id_movie'] = $show->getMovie()->getId();
        $parameters['id_cinema'] = $idCinema;
        
        $connection = Connection::getInstance();
        try{
            $resultSet = $connection->execute($query,$parameters);
    
            return $resultSet;
        }
        catch(\Exception $ex){
            throw $ex;
        }
    }

    public function verifyShowDatetimeOverlap($_show){
        $query = "SELECT s.*, m.*, r.* FROM shows s
        inner join movies m on m.id_movie = s.idMovie_show
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        WHERE (DATE(s.datetime_show) = DATE(DATE_SUB(:datetime_show, INTERVAL 1 DAY))
        OR DATE(s.datetime_show) = DATE(:datetime_show)
        OR DATE(s.datetime_show) = DATE(DATE_ADD(:datetime_show, INTERVAL 1 DAY)))
        AND (s.idRoom_show = :idRoom_show)";

        $parameters['datetime_show'] = $_show->getDatetime();
        $parameters['idRoom_show'] = $_show->getRoom()->getId();
        
        $connection = Connection::getInstance();
        $resultSet = $connection->executeWithAssoc($query,$parameters);

        $results = array();

        foreach ($resultSet as $show){
            $object = new show(
                            new Movie(
                                $show['id_movie'],
                                $show['title_movie'], 
                                $show['overview_movie'], 
                                $show['img_movie'], 
                                $show['language_movie'],                                    
                                null,
                                $show['releaseDate_movie'],
                                $show['duration_movie']),
                            new Room(
                                    $show['id_room'],
                                    $show['price_room'],
                                    $show['capacity_room'],
                                    $show['idCinema_room']),
                            $show['datetime_show']);

            $object->setId($show['id_show']);
            $results[] = $object;
        }
        return $results;
    }

    public function getByIdMovie($id){
        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.name_cinema FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        WHERE m.id_movie = :id_movie
        ORDER BY s.datetime_show";

        $parameters['id_movie'] = $id;

        $connection = Connection::getInstance();
        $resultSet = $connection->executeWithAssoc($query, $parameters);

        $results = array();

        foreach ($resultSet as $show){
            $object = new show(
                                new Movie(
                                    $show['id_movie'],
                                    $show['title_movie'], 
                                    $show['overview_movie'], 
                                    $show['img_movie'], 
                                    $show['language_movie'],                                    
                                    $this->genreDaos->getByMovie($show['id_movie']),                                    
                                    $show['releaseDate_movie'],
                                    $show['duration_movie']),
                                new Room(
                                    $show['name_room'],
                                    $show['price_room'],
                                    $show['capacity_room'],
                                    $show['idCinema_room']
                                ),
                                $show['datetime_show']
                            );
            $object->getRoom()->setId($show['id_room']);
            $object->setId($show['id_show']);
            $results[] = $object;
        }

        return $results;
    }
}
?>