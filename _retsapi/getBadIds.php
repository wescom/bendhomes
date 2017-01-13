<?php

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
                        echo $item." good";
                } else {
                        echo $item."bad";
                        $badString .= $numsArr[1].",";
                        $badCount++;
                }
        }

        $badFile = "./IdTextFiles/badIds.txt";
        file_put_contents($badFile, $badString);
        echo " Bad property count: ".$badCount;

?>