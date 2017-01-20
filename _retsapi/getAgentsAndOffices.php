<?php
//include("inc/retsabspath.php");
include("/var/www/html/_retsapi/inc/header.php");
include("/var/www/html/_retsapi/AgentOfficesFunctions.php");
ini_set('max_execution_time', 0);

executeGetAgentsAndOffices();

?>