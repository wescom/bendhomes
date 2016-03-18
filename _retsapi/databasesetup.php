<?php
include("/inc/abspath.php");
include(ABSPATH."/inc/header.php");

function accessProtected($obj, $prop) {
  $reflection = new ReflectionClass($obj);
  $property = $reflection->getProperty($prop);
  $property->setAccessible(true);
  return $property->getValue($obj);
}

/* ########## Meta Classes ########## */
function BHgetMetaClasses($rets,$meta) {
  $classes = $rets->GetClassesMetadata($meta);
  $arclasses = (array) $classes;
  $outputclasses = array();
  foreach($arclasses as $key => $val) {
    foreach($val as $tkey => $tval) {
      $tv[$tkey] = accessProtected($tval,'values');
    }
  }
  // print_r($tv);
  return $tv;
}

function BHgetMetaChildren($rets,$parents) {
  $classes = array();
  foreach($parents as $parent) {
    // echo $parent;
    // echo '<br/>'."\n";
    $classes[$parent] = BHgetMetaClasses($rets, $parent);
  }
  return $classes;
}

function BHgetMetaFields($rets,$resources,$uqueries) {
  $alldata = array();
  foreach($resources as $resourckey => $resourceval) {

    foreach($resourceval as $classname) {
      $resource = $classname['Resource']; // Property
      $class = $classname['ClassName']; // RESI
      $query = ''.$uqueries[$resource][$class].', (LastModifiedDateTime=1950-02-10T00:00:00+)';

      $results = $rets->Search(
          $resource,
          $class,
          $query,
          [
              'QueryType' => 'DMQL2', // it's always use DMQL2
              'Count' => 1, // count and records
              'Format' => 'COMPACT-DECODED',
              'Limit' => 1,
              'StandardNames' => 0, // give system names
          ]
      );

      $temparr = $results->toArray();
      $resources[$resource][$class]['datastructure'] = $temparr;
      unset($resource, $class, $query);
    }

  }
  return $resources;
}

/*
##### Let's do a loop #####
This takes the meta parent, then gets the meta information about that parent, such as
ClassName, VisibleName, Description,
*/

$bhresources = array(
  'ActiveAgent',
  'Agent', // (not used by COAR, but list anyway)
  'MemberAssociation',
  'Office',
  'OfficeAssociation',
  'OpenHouse',
  'Property'
);



$bhclasses = BHgetMetaChildren($rets,$bhresources);
$bhmetadetails = BHgetMetaFields($rets,$bhclasses,$universalqueries);

// print_r($bhmetadetails);

/*
Resource
ClassName (Class)
Description
*/

$sqls = createdb($bhmetadetails, $universalkeys);

function createdb($data, $primarykeys) {
  $sqlstatements = array();
  print_r($data);

  foreach($data as $resourcekey => $resourceval) {
    // print_r($resourcekey);
    foreach($resourceval as $classname => $classvars) {
      $sqltablename = $resourcekey.'_'.$classname;
      // echo $sqltablename."\n";
      // print_r($classvars);
      $rets_metadata = $classvars['datastructure'][0];
      $key_field = $primarykeys[$resourcekey][$classname];
      $field_prefix = '';

      if (count($rets_metadata) > 0) {
        // echo "\ntest200\n";
        // print_r($rets_metadata);
        $sqlstatements[$sqltablename] = create_table_sql_from_struct($sqltablename, $rets_metadata, $key_field, $field_prefix = "");
      }
      // $sqlstatements[$sqltablename] = '';
      // $sqlstatements[$sqltablename] = create_table_sql_from_struct($sqltablename, $rets_metadata, $key_field, $field_prefix = "");
    }

  }
  print_r($sqlstatements);

}

// $sql = create_table_sql_from_struct($table_name, $rets_metadata, $key_field, $field_prefix = "bh")

function create_table_sql_from_struct($table_name, $rets_metadata, $key_field, $field_prefix) {
    $sql_query = "CREATE TABLE ".$table_name." (\n";

    foreach ($rets_metadata as $fieldname => $fieldval) {

        // echo $fieldname."\t => \t".$fieldval."\n";

        $cleaned_comment = addslashes('initial build');
        // $sql_make = "\t`" . $field_prefix . $field->getSystemName()."` ";
        // $sql_make = "\t`" . $field_prefix . $fieldname ."` ";
        $sql_make = "\n" . $field_prefix . $fieldname ." ";

        if ($fieldname == "Fullname") {
          $sql_make .= "VARCHAR(50)";
        } elseif ($fieldname == "IsActive") {
          $sql_make .= "CHAR(1)";
        } elseif ($fieldname == "LastModifiedDateTime") {
          $sql_make .= "DATETIME default '0000-00-00 00:00:00' NOT NULL";
        } elseif ($fieldname == "MemberNumber") {
          $sql_make .= "INT(10)";
        } elseif ($fieldname == "MLSID") {
          $sql_make .= "VARCHAR(12)";
        } elseif ($fieldname == "OfficeMLSID") {
          $sql_make .= "VARCHAR(12)";
        } elseif ($fieldname == "OfficeName") {
          $sql_make .= "VARCHAR(30)";
        } elseif ($fieldname == "OfficeNumber") {
          $sql_make .= "INT(10)";
        } elseif ($fieldname == "ContactPhoneAreaCode1") {
          $sql_make .= "CHAR(3)";
        } elseif ($fieldname == "ContactPhoneNumber1") {
          $sql_make .= "CHAR(7)";
        } elseif ($fieldname == "ContactPhoneNumber1") {
          $sql_make .= "CHAR(7)";
        } elseif ($fieldname == "IDX") {
          $sql_make .= "CHAR(3)";
        } elseif ($fieldname == "OfficePhoneComplete") {
          $sql_make .= "CHAR(12)";
        } elseif ($fieldname == "StreetAddress") {
          $sql_make .= "CHAR(30)";
        } elseif ($fieldname == "StreetCity") {
          $sql_make .= "CHAR(21)";
        } elseif ($fieldname == "StreetState") {
          $sql_make .= "CHAR(2)";
        } elseif ($fieldname == "StreetZipCode") {
          $sql_make .= "CHAR(5)";
        } elseif ($fieldname == "Area") {
          $sql_make .= "CHAR(10)";
        } elseif ($fieldname == "City") {
          $sql_make .= "CHAR(10)";
        } else {
            $sql_make .= "VARCHAR(255)";
        }

        /*
        if ($fieldname == "LookupMulti") {
            $sql_make .= "TEXT";
        } elseif ($field->getInterpretation() == "Lookup") {
            $sql_make .= "VARCHAR(50)";
        } elseif ($field->getDataType() == "Int" || $field->getDataType() == "Small" || $field->getDataType() == "Tiny") {
            $sql_make .= "INT(".$field->getMaximumLength().")";
        } elseif ($field->getDataType() == "Long") {
            $sql_make .= "BIGINT(".$field->getMaximumLength().")";
        } elseif ($field->getDataType() == "DateTime") {
            $sql_make .= "DATETIME default '0000-00-00 00:00:00' NOT NULL";
        } elseif ($fieldname == "Fullname" && $field->getMaximumLength() <= 255) {
            $sql_make .= "VARCHAR(".$field->getMaximumLength().")";
        } elseif ($field->getDataType() == "Character" && $field->getMaximumLength() > 255) {
            $sql_make .= "TEXT";
        } elseif ($field->getDataType() == "Decimal") {
            $pre_point = ($field->getMaximumLength() - $field->getPrecision());
            $post_point = !empty($field->getPrecision()) ? $field->getPrecision() : 0;
            $sql_make .= "DECIMAL({$field->getMaximumLength()},{$post_point})";
        } elseif ($field->getDataType() == "Boolean") {
            $sql_make .= "CHAR(1)";
        } elseif ($field->getDataType() == "Date") {
            $sql_make .= "DATE default '0000-00-00' NOT NULL";
        } elseif ($field->getDataType() == "Time") {
            $sql_make .= "TIME default '00:00:00' NOT NULL";
        } else {
            $sql_make .= "VARCHAR(255)";
        }
        */
        // $sql_make .=  " COMMENT '".$cleaned_comment."',\n";
        $sql_make .= ",";
        $sql_query .= $sql_make;
    }

    // $sql_query .=  "PRIMARY KEY(`".$field_prefix.$key_field."`) )";
    $sql_query .=  "PRIMARY KEY(".$field_prefix.$key_field.") )";
    return $sql_query;
}


/*
CREATE TABLE ActiveAgent_MEMB (
FullName VARCHAR(255),
IsActive VARCHAR(3),
MemberNumber INT(10),
MLSID VARCHAR(12),
OfficeMLSID VARCHAR(12),
OfficeName VARCHAR(30),
OfficeNumber INT(10),
PRIMARY KEY(MemberNumber) )
*/



















/* ########## Get MetaData fields by class ########## */




// $system = $rets->GetSystemMetadata();

// $classes = $rets->GetClassesMetadata('Property');





// print_r($rets_resource_info);

/*

  $resource = "Property";
  $class = "RESI";
  // or set through a loop

  // pull field format information for this class
  $rets_metadata = $rets->GetSystemMetadata($resource, $class);

  $table_name = "rets_".strtolower($resource)."_".strtolower($class);
  // i.e. rets_property_res

  $sql = create_table_sql_from_metadata($table_name, $rets_metadata, "L_ListingID");

  // mysql_query($sql);

  // functions

  function create_table_sql_from_metadata($table_name, $rets_metadata, $key_field, $field_prefix = "") {
      $sql_query = "CREATE TABLE ".$table_name." (\n";
      foreach ($rets_metadata as $field) {
          $cleaned_comment = addslashes($field->getLongName());
          $sql_make = "\t`" . $field_prefix . $field->getSystemName()."` ";
          if ($field->getInterpretation() == "LookupMulti") {
              $sql_make .= "TEXT";
          } elseif ($field->getInterpretation() == "Lookup") {
              $sql_make .= "VARCHAR(50)";
          } elseif ($field->getDataType() == "Int" || $field->getDataType() == "Small" || $field->getDataType() == "Tiny") {
              $sql_make .= "INT(".$field->getMaximumLength().")";
          } elseif ($field->getDataType() == "Long") {
              $sql_make .= "BIGINT(".$field->getMaximumLength().")";
          } elseif ($field->getDataType() == "DateTime") {
              $sql_make .= "DATETIME default '0000-00-00 00:00:00' NOT NULL";
          } elseif ($field->getDataType() == "Character" && $field->getMaximumLength() <= 255) {
              $sql_make .= "VARCHAR(".$field->getMaximumLength().")";
          } elseif ($field->getDataType() == "Character" && $field->getMaximumLength() > 255) {
              $sql_make .= "TEXT";
          } elseif ($field->getDataType() == "Decimal") {
              $pre_point = ($field->getMaximumLength() - $field->getPrecision());
              $post_point = !empty($field->getPrecision()) ? $field->getPrecision() : 0;
              $sql_make .= "DECIMAL({$field->getMaximumLength()},{$post_point})";
          } elseif ($field->getDataType() == "Boolean") {
              $sql_make .= "CHAR(1)";
          } elseif ($field->getDataType() == "Date") {
              $sql_make .= "DATE default '0000-00-00' NOT NULL";
          } elseif ($field->getDataType() == "Time") {
              $sql_make .= "TIME default '00:00:00' NOT NULL";
          } else {
              $sql_make .= "VARCHAR(255)";
          }
          $sql_make .=  " COMMENT '".$cleaned_comment."',\n";
          $sql_query .= $sql_make;
      }
      $sql_query .=  "PRIMARY KEY(`".$field_prefix.$key_field."`) )";
      return $sql_query;
  }

*/

?>
