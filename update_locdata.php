<html>

   <head>

    <meta charset="UTF-8">
    <title>Update Locdata</title>

  </head>

<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

/*IMPORTANT GLOBALS*/
include 'dbinfo.php';

$pdostring = 'mysql:host=' . $host . ';dbname=' . $dbname . ';charset=utf8';

//open the mysql connection using a PDO interface object
$dbh = new PDO($pdostring, $user, $pass);
$dbh->exec("SET names 'utf8';");

  $row = 1;

  //maybe add some sort of form to specify filename

  $filename = "test.csv"; //change this as needed when data comes over from history dept
  $special_character = '#'; //change this if we decide on a different special character

  if(($handle = fopen($filename, "r")) !== FALSE) { //open filename in same directory as script

  	echo "Opening file " . $filename . " was successful. <br><br>";

  	while(($data = fgetcsv($handle,0, ",")) !== FALSE) {

  		if($row >= 2) { //moved past the row of column names, now into the actual data

  			//important note: if the script does not behave as expected in the future, check that the indexes
  			//are still correct for whatever spreadsheets/csv's are being used

        $tableindex = $row+1;

        //echo "Field 0 is " . $data[0] . "<br>"; //lastname
        //echo "Field 1 is " . $data[1] . "<br>"; //firstname
        //echo "Field 2 is " . $data[2] . "<br>"; //middle
        echo "Field 3 is " . $data[3] . "<br>"; //gender
       // echo "Field 4 is " . $data[4] . "<br>"; //FILENAME
        //echo "Field 5 is " . $data[5] . "<br>"; //LETTERDATE
        //echo "Field 6 is " . $data[6] . "<br>"; //pages
        //echo "Field 7 is " . $data[7] . "<br>"; //scanned?
        //echo "Field 8 is " . $data[8] . "<br>"; //transcribed?
        //echo "Field 9 is " . $data[9] . "<br>"; //needs transcription?
        //echo "Field 10 is " . $data[10] . "<br>"; //FULL ADDRESS - USED FOR GEOCODING, NEEDS # REPLACEMENT
        echo "Field 11 is " . $data[11] . "<br>"; //location
        echo "Field 12 is " . $data[12] . "<br>"; //street
        echo "Field 13 is " . $data[13] . "<br>"; //city
        echo "Field 14 is " . $data[14] . "<br>"; //state
        echo "Field 15 is " . $data[15] . "<br>"; //country
        echo "Field 16 is " . $data[16] . "<br>"; //zipcode
        echo "Field 17 is " . $data[17] . "<br>"; //service branch
        echo "Field 18 is " . $data[18] . "<br>"; //specialty

        $gender = str_replace(array($special_character),',',$data[3]);
        $service_branch = str_replace(array($special_character),',',$data[17]);
        $specialty = str_replace(array($special_character),',',$data[18]);

        $location_name = str_replace(array($special_character),',',$data[11]);
        $location_street = str_replace(array($special_character),',',$data[12]);
        $location_city = str_replace(array($special_character),',',$data[13]);
        $location_state = str_replace(array($special_character),',',$data[14]);
        $location_country = str_replace(array($special_character),',',$data[15]);
        $location_zipcode = str_replace(array($special_character),',',$data[16]);


        //$filename = $data[4];
  			//$location = $data[10]; //the full address column of the csv, WILL have some special character instead of commas, need to convert

        /*
  			$locationid = some arbitrary number 
        set the id as a var in this script and change/increment it as appropriate during the loop
        */

  			//$location = str_replace(array($special_character),',',$location); //replace the special character with commas after importing the csv file
        
        /*
        $query = $dbh->prepare('UPDATE letters SET gender = :gender, service_branch = :service_branch, specialty = :specialty WHERE id = :id');
        $query->bindParam(':gender', $gender);
        $query->bindParam(':service_branch', $service_branch);
        $query->bindParam(':specialty', $specialty);
        //$query->bindParam(':filename', $data[4]);
        //$query->bindParam(':letterdate', $data[5]);
        $query->bindParam(':id', $tableindex);
        $query->execute(); */

        $query = $dbh->prepare('UPDATE locdata SET location_name = :location_name, location_street = :location_street, location_city = :location_city, location_state = :location_state, location_country = :location_country, location_zipcode = :location_zipcode WHERE locationid = :id');
        $query->bindParam(':location_name', $location_name);
        $query->bindParam(':location_street', $location_street);
        $query->bindParam(':location_city', $location_city);
        $query->bindParam(':location_state', $location_state);
        $query->bindParam(':location_country', $location_country);
        $query->bindParam(':location_zipcode', $location_zipcode);

        $query->bindParam(':id', $tableindex);
        $query->execute(); 

  			//UPDATE code goes here to automate entering all of the full addresses into the mysql db
          //echo "UPDATE letters,locdata SET locdata.location =" . $location . " WHERE letters.filename = " . $filename . " AND letters.id = locdata.locationid <br>";

          

        	//$query = $dbh->prepare('UPDATE letters,locdata SET locdata.location = :location WHERE letters.filename = :filename AND letters.id = locdata.locationid');
        	//$query->bindParam(':location',$location);
          //$query->bindParam(':filename',$filename);
        	//$query->execute(); 

        	//echo "Table locdata: Updated address \"" . $location . "\" at filename " . $filename . "<br>";
  		}

  		$row++;
  	}

    //$query = $dbh->prepare('UPDATE letters,locdata SET locdata.location = "test" WHERE locdata.locationid = 23');
    //$query->execute(); 


  	fclose($handle);
  }

  else {

  	echo "There was a problem opening file " . $filename . "<br>";
  }
  
 ?>

 </html>