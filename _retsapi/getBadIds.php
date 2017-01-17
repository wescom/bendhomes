<?php

        function markBadInOurRets($badString){
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

                $conn->close();
        }



        $file = "./IdTextFiles/retsIds.txt";
        $retsString = file_get_contents($file);
        echo "rets: ".$retsString;

        $ourFile = "./IdTextFiles/ourIds.txt";
        $ourString = file_get_contents($ourFile);

        $ourArray = explode(",", $ourString);
        $retsArray = explode(",", $retsString);

        $badCount = 0;
        $badString = "";
        foreach($ourArray as $item) {
                $numsArr = explode("-", $item);
                if (in_array($numsArr[0], $retsArray)){
                        echo "<pre>".$item." - good</pre>";
                } else {
                        echo "<pre>".$item."bad</pre>";
                        $badString .= $numsArr[1].",";
                        $badCount++;
                }
        }

        $badFile = "./IdTextFiles/badIds.txt";
        file_put_contents($badFile, $badString);
        echo " Bad property count: ".$badCount;

        markBadInOurRets($badString);

?>