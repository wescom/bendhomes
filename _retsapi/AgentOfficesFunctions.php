<?php

function getPhotos($qvars, $itemsarr, $pullDate) {
  global $universalkeys;
  global $rets;
  if ($qvars['class'] == 'OFFI') {
    $dataType = 'OfficeNumber';
  } else {
    $dataType = 'MemberNumber';
  }

  foreach($itemsarr as $prop)  {
    $puid = $universalkeys[$qvars['resource']][$qvars['class']];
    $photoName = "";
    if ($qvars['fotos'] == 'yes') {
      unset($photos);
      $photos = $rets->GetObject($qvars['resource'], 'Photo', $prop[$puid],'*', 0);
      foreach($photos as $photo) {
        if ($photo->getObjectId() != '*') {
          $photoName = $prop[$dataType].'_'.$photo->getObjectId().'.jpg';
          $photoPath = '/var/www/html/_retsapi/imagesAgents/'.$photoName;
          $photobinary = $photo->getContent();
          file_put_contents($photoPath, $photobinary, LOCK_EX);
          echo "<pre style='color:blue'>Saving photo: ".$photoPath."</pre>";

        }
      }
      $itemsarr[$prop[$puid]]['images'] = $photoName;

      $pulldate = (int) time();
      $itemsarr[$prop[$puid]]['lastPullTime'] = date('c',$pulldate);
    }
    
   
  }
  return $itemsarr;
}

function saveToDB($itemsarr, $qvars){

  //echo "saving to db";
  $db = array(
    'host' => 'localhost',
    'username' => 'phrets',
    'password' => 'hCqaQvMKW9wJKQwS',
    'database' => 'bh_rets'
  );
  $dbConnection = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);
  unset($db);
  if($qvars['resource'] == 'Office') {
    $tableItemsArray = ['IDX','IsActive','LastModifiedDateTime','MLSID','OfficeName','OfficeNumber','OfficePhone','OfficePhoneComplete','StreetAddress','StreetCity','StreetState','StreetZipCode','lastPullTime']; 
  } else if ($qvars['resource'] == 'ActiveAgent') {
    $tableItemsArray = ['FullName','IsActive','LastModifiedDateTime','MLSID','OfficeMLSID','OfficeName','OfficeNumber','images','lastPullTime'];
  } else {
    $tableItemsArray = ['ContactAddlPhoneType1','ContactPhoneAreaCode1','ContactPhoneNumber1','ContactAddlPhoneType2','ContactPhoneAreaCode2','ContactPhoneNumber2','ContactAddlPhoneType3','ContactPhoneAreaCode3','ContactPhoneNumber3','FullName','IsActive','LastModifiedDateTime','MemberNumber','MLSID','images','lastPullTime'];
  }

  foreach($itemsarr as $key => $array) {
    $escarray = array_map('mysql_real_escape_string', $array);


    $query = "INSERT INTO ".$qvars['resource']."_".$qvars['class'];
    $query .= " (`".implode("`, `", array_keys($escarray))."`)";
    $query .= " VALUES ('".implode("', '", $escarray)."') ";
    $query .= "ON DUPLICATE KEY UPDATE ";  //MemberNumber = VALUES(".$array['MemberNumber'].")";

    $isFirst = 1;
    foreach($tableItemsArray as $item) {
      if ($isFirst == 1) {
        $query .= $item." = VALUES(".$item.")";
        $isFirst = 0;
      } else {
        $query .= ", ".$item." = VALUES(".$item.")";

      }
    }
    echo '<pre style="color:red">Query: '.$query.'</pre>';
    if (mysqli_query($dbConnection, $query)) {
        echo "<p style='margin: 0; background-color: green; color: #fff;'>Successfully inserted " . mysqli_affected_rows($dbConnection) . " row</p>";
    } else {
        echo "<p style='margin: 0; background-color: red; color: #fff;'>Error occurred: " . mysqli_error($dbConnection) . " row</p>";;
    }
  }
  mysqli_close($dbConnection);
}


?>