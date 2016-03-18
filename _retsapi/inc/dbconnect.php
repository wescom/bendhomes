<?php

$db = array(
  'host' => 'localhost',
  'username' => 'phrets',
  'password' => 'hCqaQvMKW9wJKQwS',
  'database' => 'bh_rets'
);

$dbConnection = mysqli_connect($db['host'], $db['username'], $db['password'], $db['database']);

unset($db);

?>
