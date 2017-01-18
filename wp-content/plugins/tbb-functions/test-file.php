<?php // Functions testing file. Not used for production

// These are our demo API keys, you can use them!
/*$username = "simplyrets";
$password = "simplyrets";
$remote_url = 'https://api.simplyrets.com/properties';

$opts = array(
    'http'=>array(
        'method'=>"GET",
        'header' => "Authorization: Basic " . base64_encode("$username:$password")
    )
);
$context = stream_context_create($opts);
$file = file_get_contents($remote_url, false, $context);
print($file);*/



/*$db = array(
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
				$idString .= $row['ListingRid']."-".$row['MLNumber']."<br>";
		}
}

$conn->close();

echo "idString: ".$idString;*/



class Rets_DB {
    // The database connection
    protected static $connection;

    /**
     * Connect to the database
     * 
     * @return bool false on failure / mysqli MySQLi object instance on success
     */
    public function connect() {    
        // Try and connect to the database
        if(!isset(self::$connection)) {
            // Load configuration as an array. Use the actual location of your configuration file
            //$config = parse_ini_file('./config.ini'); 
            self::$connection = new mysqli('localhost', 'phrets', 'hCqaQvMKW9wJKQwS', 'bh_rets');
        }

        // If connection was not successful, handle the error
        if(self::$connection === false) {
            // Handle error - notify administrator, log to a file, show an error screen, etc.
            return false;
        }
        return self::$connection;
    }

    /**
     * Query the database
     *
     * @param $query The query string
     * @return mixed The result of the mysqli::query() function
     */
    public function query($query) {
        // Connect to the database
        $connection = $this -> connect();

        // Query the database
        $result = $connection -> query($query);

        return $result;
    }

    /**
     * Fetch rows from the database (SELECT query)
     *
     * @param $query The query string
     * @return bool False on failure / array Database rows on success
     */
    public function select($query) {
        $rows = array();
        $result = $this -> query($query);
        if($result === false) {
            return false;
        }
        while ($row = $result -> fetch_assoc()) {
            $rows[] = $row;
        }
        return $rows;
    }

    /**
     * Fetch the last error from the database
     * 
     * @return string Database error message
     */
    public function error() {
        $connection = $this -> connect();
        return $connection -> error;
    }

    /**
     * Quote and escape value for use in a database query
     *
     * @param string $value The value to be quoted and escaped
     * @return string The quoted and escaped string
     */
    public function quote($value) {
        $connection = $this -> connect();
        return "'" . $connection -> real_escape_string($value) . "'";
    }
}

$agents_query = new Rets_DB();
$agents = $agents_query -> select("select * from ActiveAgent_MEMB");

print_r($agents);

$agent;
foreach( $agents as $row ) {
	$agent .= 
		'<p>
		Name: ' .$row['FullName'] .'<br>
		Is Active: '. $row['IsActive'] .'<br>
		Member Number: '. $row['MemberNumber'] .'<br>
		MLS ID: '. $row['MLSID'] .'<br>
		Office MLS ID: '. $row['OfficeMLSID'] .'<br>
		Office Name: '. $row['OfficeName'] .'<br>
		Office Number: '. $row['OfficeNumber'] .'<br>
		Is Featured: '. $row['featured'] .
		'</p>';
}

echo $agent;

