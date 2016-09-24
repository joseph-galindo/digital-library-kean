<?php

/*IMPORTANT GLOBALS*/ 
include 'dbinfo.php';

header("Content-type: text/html; charset=utf-8");

$pdostring = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

//open the mysql connection using a PDO interface object
$dbh = new PDO($pdostring, $user, $pass);

//get every entry in the locdata table
//REMEMBER TO CHANGE THE LOCATIONID AS NEEDED, TRY SMALL CASES FIRST
$query = $dbh->prepare('SELECT * FROM locdata WHERE locationid =3');
$query->execute();

$results = $query->fetchAll();

foreach($results as $row) {

  $address = $row['location'];
  //prep the address
  //$address = str_replace(" ",'+',$address);

  //send request to google 
  if(!empty($address) && strcmp($address, "N/A") != 0) { //if we actually have an address and it is not "N/A", geocode it

    $address = preg_replace('/\x{00a0}/u', ',' , $address); //get rid of non breaking space char
    $goog = "http://maps.googleapis.com/maps/api/geocode/xml?address=" . rawurlencode($address) . "&sensor=false";
    echo $goog . "<br>";

      $xmlfile = simplexml_load_file($goog);

      if($xmlfile !== FALSE) {

        //get the simplexml objects
        //print_r($xmlfile);
        
        $latitude = (float)$xmlfile->result->geometry->location->lat;
        $longitude = (float)$xmlfile->result->geometry->location->lng;

        //print_r($latitude);
        //print_r($longitude);
        //cast the simplexml objects to float values, so they can be used later
        //$latitude = (float)$latitude[0];
        //$longitude = (float)$longitude[0];

        //UPDATE code goes here to automate entering all of the latitudes/longtiudes into the mysql db
        $query = $dbh->prepare('UPDATE locdata SET lat = :lat, lng = :lng WHERE locationid = :id');
        $query->bindValue(':id',$row['locationid'],PDO::PARAM_INT);
        $query->bindParam(':lat',$latitude);
        $query->bindParam(':lng',$longitude);
        $query->execute();

        echo "The letter id is " . $row['locationid'] . "<br>";
        echo "The address is " . $row['location'] . "<br>";
        echo $latitude . "<br>";
        echo $longitude . "<br>";
        echo "<br>"; 
      }

      else
        echo "Could not get an xmlfile for letter id " . $row['locationid'] . "<br>"; 
  }
} 

?>