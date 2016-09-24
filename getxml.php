<?php

  include 'dbinfo.php';

  //open the mysql connection using a PDO interface object
  $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8', $user, $pass);

//firstname + lastname given
if(isset($_GET['firstname']) && !empty($_GET['firstname']) && (isset($_GET['lastname']) && !empty($_GET['lastname']))) {
  	
 	  $firstname = $_GET['firstname'];
  	$lastname = $_GET['lastname'];

  	$query = $dbh->prepare('SELECT * FROM letters,locdata WHERE firstname = :firstname AND lastname = :lastname AND letters.id = locdata.locationid ORDER BY ts_dateguess');
 
    $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
    $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);

    $query->execute();

    $results = $query -> fetchAll();
}

else if(isset($_GET['letterid']) && !empty($_GET['letterid']) && is_numeric($_GET['letterid'])){

    $letterid = $_GET['letterid'];

    $query = $dbh->prepare('SELECT * FROM letters, locdata WHERE id = :lid AND letters.id = locdata.locationid');

    $query->bindValue(':lid',$letterid,PDO::PARAM_INT);

    $query->execute();

    $results = $query -> fetchAll();
}

else if(isset($_GET['year']) && !empty($_GET['year']) && is_numeric($_GET['year'])) {

    $year = $_GET['year'];

    $query = $dbh->prepare('SELECT * FROM letters, locdata WHERE YEAR(ts_dateguess) = :year AND letters.id = locdata.locationid ORDER BY ts_dateguess');

    $query->bindValue(':year',$year,PDO::PARAM_INT);

    $query->execute();

    $results = $query -> fetchAll();
}

else if(isset($_GET['year']) && !empty($_GET['year']) && is_string($_GET['year'])) {

    $year = $_GET['year'];

    $year = strtolower($year);

    if(strcmp("all",$year) === 0) {

      $query = $dbh->prepare('SELECT * FROM letters, locdata WHERE YEAR(ts_dateguess) IS NOT NULL AND letters.id = locdata.locationid ORDER BY ts_dateguess');

      $query->execute();

      $results = $query -> fetchAll();
    }
}

else {

  echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to access records.</p>'; 
  die();
}

function parseToXML($htmlString) {

$xmlStr=htmlspecialchars($htmlString);
return $xmlStr;
}

header("Content-type: text/xml; charset=utf-8");

//start the XML file, echo the parent node
echo '<?xml version="1.0" encoding="UTF-8"?>';
echo '<markers>';

//Go through each entry, print XML nodes for each
foreach($results as $row) {

	 echo '<marker ';
	 echo 'name="' . parseToXML($row['filename']) . '" ';
	 echo 'address="' . parseToXML($row['location']) . '" ';
	 echo 'lat="' . $row['lat'] . '" ';
	 echo 'lng="' . $row['lng'] . '" ';
   echo 'letterid="' . $row['id'] . '" ';
   echo 'year="' . substr($row['ts_dateguess'],0,4) . '" ';
	 echo '/>';
}

//end XML file
echo '</markers>';

?>