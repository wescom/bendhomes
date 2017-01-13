<?php

$db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );

        $idString = "";

        $conn = new mysqli($db['host'], $db['username'], $db['password'], $db['database']);

        if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
        }

        $query = "select ListingRid, MLNumber from Property_BUSI";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }

        $query = "select ListingRid, MLNumber from Property_COMM";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }

        $query = "select ListingRid, MLNumber from Property_FARM";
        $result = $conn->query($query);

                if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }

        $query = "select ListingRid, MLNumber from Property_LAND";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }

        $query = "select ListingRid, MLNumber from Property_MULT";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }

        $query = "select ListingRid, MLNumber from Property_RESI";
        $result = $conn->query($query);

        if ($result->num_rows > 0) {

                while($row = $result->fetch_assoc()) {
                        //echo "<pre>id: ".$row['ListingRid']."</pre>";
                        $idString .= $row['ListingRid']."-".$row['MLNumber'].",";
                }
        }



        $conn->close();

        echo "idString: ".$idString;

        $file = "ourIds.txt";
        file_put_contents($file, $idString);
?>