<?php

        include_once("/var/www/html/_retsapi/propertiesFunctions.php");

        executeUpdatePropertiesTable();

        cleanPropertiesTable();

        executeUpdateOpenHousesTable();

        cleanOpenHousesTable();

?>