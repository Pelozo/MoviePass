<?php
namespace daos;
use daos\baseDaos as BaseDaos;
use models\show as Show;
use models\movie as Movie;
use models\room as Room;

class ShowDaos extends BaseDaos{

    const TABLE_NAME = "shows";
    const SHOW_INTERVAL = 15;

    public function __construct(){
        parent::__construct(self::TABLE_NAME, 'Show');        
    }

    public function getAll(){
        //var_dump(parent::_getAll());

        $query = "SELECT s.id_show, s.datetime_show, m.*, r.*, c.name_cinema FROM shows s
        INNER JOIN movies m ON s.idMovie_show = m.id_movie
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON r.idCinema_room = c.id_cinema
        ORDER BY s.datetime_show";

        $connection = Connection::getInstance();
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
                                    null,                                    
                                    $show['releaseDate_movie'],
                                    $show['duration_movie']),
                                new Room(
                                    $show['id_room'],
                                    $show['price_room'],
                                    $show['capacity_room'],
                                    $show['idCinema_room']
                                ),
                                $show['datetime_show']
                            );
            $object->setId($show['id_show']);
            $results['shows'][] = $object;

            $results['cinemas'][] = $show['name_cinema'];
        }

        return $results;
        
    }

    public function exists($id){
        return parent::_exists($id);
    }

    public function getById($id){
        return parent::_getByProperty($id, 'id');
    }

    public function add($show){

        $query = "INSERT INTO " . self::TABLE_NAME . " (idMovie_show, datetime_show, idRoom_show)
                                                        values(:idMovie_show, :datetime_show, :idRoom_show);";

        $params['idMovie_show'] = $show->getIdMovie()->getId();
        $params['idRoom_show'] = $show->getIdRoom()->getId();
        $params['datetime_show'] = $show->getDatetime();

        $this->connection = Connection::getInstance();
        return $this->connection->executeNonQuery($query, $params);

    }

    public function remove($id){
        return parent::_remove($id, 'id');
    }

    public function modify($show){
        return parent::_modify($show, $show->getId(), "id");
    }

    public function verifyShowDay($show, $idCinema){
        
        $query = 'SELECT c.id_cinema, s.idMovie_show, s.datetime_show from ' . self::TABLE_NAME . ' s
        INNER JOIN movies m ON m.id_movie = s.idMovie_show
        INNER JOIN rooms r ON s.idRoom_show = r.id_room
        INNER JOIN cinemas c ON c.id_cinema = r.idCinema_room
        WHERE DAY(s.datetime_show) = DAY(:datetime_show) AND s.idMovie_show = :id_movie AND r.idCinema_room != :id_cinema;';
        

        $parameters['datetime_show'] = $show->getDatetime();
        $parameters['id_movie'] = $show->getIdMovie()->getId();
        $parameters['id_cinema'] = $idCinema;
        
        $connection = Connection::getInstance();
        $resultSet = $connection->execute($query,$parameters);

        return $resultSet;
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
        $parameters['idRoom_show'] = $_show->getIdRoom()->getId();
        
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
}
?>