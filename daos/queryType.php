<?php
    namespace daos;
    
    /*
        "enums? not in my langauge!"

                    - php devs, probably.

    */
    abstract class QueryType{
        const Query = 0;
        const StoredProcedure = 1;
    }
?>