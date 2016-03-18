<?php

include('./inc/header.php');

// gets resource information.  need this for the KeyField
$rets_resource_info = $rets->GetMetadataInfo();

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

?>
