<?php

include("inc/retsabspath.php");
include(RETSABSPATH."/inc/header.php");

$object_types = $rets->GetMetadataObjects("Property");

print_r($rets);

foreach ($object_types as $type) {
        echo "+ Object {$type['ObjectType']} described as " . $type['Description'] . "\n";
}

?>
