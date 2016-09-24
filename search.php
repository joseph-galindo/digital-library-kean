
<?php 
  
  //initialize some helper vars

  $firstname = NULL;
  $lastname = NULL;
  $middlename = NULL;
  $specialty = NULL;

  $datacount = 0;
  $namelength = 3;
  $middlenamelength = 1;

  $months = array("--" => "--",
                  "01" => "January",
                  "02" => "February",
                  "03" => "March",
                  "04" => "April",
                  "05" => "May",
                  "06" => "June",
                  "07" => "July",
                  "08" => "August",
                  "09" => "September",
                  "10" => "October",
                  "11" => "November",
                  "12" => "December");

  $querystring = 'SELECT * FROM letters, locdata WHERE';
  $errortext = NULL;

  if (isset($_GET['firstname']) && !empty($_GET['firstname']))
    $hasSubmittedFirstName = TRUE;
  else
    $hasSubmittedFirstName = FALSE;
  
  if (isset($_GET['lastname']) && !empty($_GET['lastname']))
    $hasSubmittedLastName = TRUE;
  else
    $hasSubmittedLastName = FALSE;

  if (isset($_GET['middlename']) && !empty($_GET['middlename']))
    $hasSubmittedMiddleName = TRUE;
  else
    $hasSubmittedMiddleName = FALSE;

  if (isset($_GET['specialty']) && !empty($_GET['specialty']))
    $hasSubmittedSpecialty = TRUE;
  else
    $hasSubmittedSpecialty = FALSE;

  if (isset($_GET['street']) && !empty($_GET['street']))
    $hasSubmittedStreet = TRUE;
  else
    $hasSubmittedStreet = FALSE;

  if (isset($_GET['city']) && !empty($_GET['city']))
    $hasSubmittedCity = TRUE;
  else
    $hasSubmittedCity = FALSE;

  if (isset($_GET['state']) && !empty($_GET['state']))
    $hasSubmittedState = TRUE;
  else
    $hasSubmittedState = FALSE;

  if (isset($_GET['country']) && !empty($_GET['country']))
    $hasSubmittedCountry = TRUE;
  else
    $hasSubmittedCountry = FALSE;

  if (isset($_GET['zipcode']) && !empty($_GET['zipcode']))
    $hasSubmittedZipcode = TRUE;
  else
    $hasSubmittedZipcode = FALSE;

  $gender = isset($_GET['gender']) && !ctype_space($_GET['gender']) ? $_GET['gender'] : "-";
  $service_branch = isset($_GET['service_branch']) && !ctype_space($_GET['service_branch']) ? $_GET['service_branch'] : "-";

  $orderType = isset($_GET['OrderType']) && !ctype_space($_GET['OrderType']) ? $_GET['OrderType'] : "chrono";

  $day = isset($_GET['day']) && !ctype_space($_GET['day']) ? $_GET['day'] : "--";
  $month = isset($_GET['month']) && !ctype_space($_GET['month']) ? $_GET['month'] : "--";
  $year = isset($_GET['year']) && !ctype_space($_GET['year']) ? $_GET['year'] : "----";
?>

<html>

   <head>

    <meta charset="UTF-8">
    <title>Nancy Thompson Library: Search Page</title>

    <style type="text/css">
      body{ font-family: "Lucida Sans", Trebuchet, monospace; }
      .searchTermBox{ margin-left: 5px; border:solid 1px #333; font-weight:bold; color:#333; padding: 5px;}
      .bold{ font-weight:bold; }
      .resultRow{ border-bottom:1px solid #ccc; }
      .alt{ background-color: #eee; } 
      a:visited { color: #800080;}
      .letterLink{text-align:;}
      .filename{margin-left:20px;}
    </style>
    
    <!-- UI stuff -->
    <script type="text/javascript" src="jquery/jquery-2.1.1.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">

   </head>

   <body>

    <?php include 'menu.php'; ?>
    <h1>Search</h1>

    <?php

      if(strcmp($day,"--") != 0)
        $datacount++;

      if(strcmp($month,"--") != 0)
        $datacount++;

      if(strcmp($year,"----") != 0)
        $datacount++;

      if(strcmp($gender,"-") != 0)
        $datacount++;

      if(strcmp($service_branch,"-") != 0)
        $datacount++;

      if($hasSubmittedFirstName) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here

        $firstname = str_replace(array('%','_'),'',$_GET['firstname']);
        $firstname = trim($firstname);

        if(ctype_space($firstname)){
        $hasSubmittedFirstName = FALSE;
        $errortext .= "<font color=\"red\">First name not searched:</font> Invalid first name. Please enter a keyword. <br>";
        }

        if(strlen($firstname) < $namelength){
        $hasSubmittedFirstName = FALSE;
        $errortext .= "<font color=\"red\">First name not searched:</font> First name is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedFirstName) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedLastName) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $lastname = str_replace(array('%','_'),'',$_GET['lastname']);
        $lastname = trim($lastname);

        if(ctype_space($lastname)){
        $hasSubmittedLastName = FALSE;
        $errortext .= "<font color=\"red\">Last name not searched:</font> Invalid last name. Please enter a keyword. <br>";
        }

        if(strlen($lastname) < $namelength){
        $hasSubmittedLastName = FALSE;
        $errortext .= "<font color=\"red\">Last name not searched:</font> Last name is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedLastName) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedMiddleName) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $middlename = str_replace(array('%','_'),'',$_GET['middlename']);
        $middlename = trim($middlename);

        if(ctype_space($middlename)){
        $hasSubmittedMiddleName = FALSE;
        $errortext .= "<font color=\"red\">Middle name not searched:</font> Invalid middle name. Please enter a keyword. <br>";
        }

        if(strlen($middlename) < $middlenamelength){
        $hasSubmittedMiddleName = FALSE;
        $errortext .= "<font color=\"red\">Middle name not searched:</font> Middle name is too short. Please enter a keyword of at least " . $middlenamelength . " characters. <br>";
        }

        if($hasSubmittedMiddleName) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedSpecialty) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $specialty = str_replace(array('%','_'),'',$_GET['specialty']);
        $specialty = trim($specialty);

        if(ctype_space($specialty)){
        $hasSubmittedSpecialty = FALSE;
        $errortext .= "<font color=\"red\">Specialty not searched:</font> Invalid specialty. Please enter a keyword. <br>";
        }

        if(strlen($specialty) < $namelength){
        $hasSubmittedSpecialty = FALSE;
        $errortext .= "<font color=\"red\">Specialty not searched:</font> Specialty is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedSpecialty) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedStreet) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $street = str_replace(array('%','_'),'',$_GET['street']);
        $street = trim($street);

        if(ctype_space($street)){
        $hasSubmittedStreet = FALSE;
        $errortext .= "<font color=\"red\">Street not searched:</font> Invalid street. Please enter a keyword. <br>";
        }

        if(strlen($street) < $namelength){
        $hasSubmittedStreet = FALSE;
        $errortext .= "<font color=\"red\">Street not searched:</font> Street is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedStreet) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedCity) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $city = str_replace(array('%','_'),'',$_GET['city']);
        $city = trim($city);

        if(ctype_space($city)){
        $hasSubmittedCity = FALSE;
        $errortext .= "<font color=\"red\">City not searched:</font> Invalid city. Please enter a keyword. <br>";
        }

        if(strlen($city) < $namelength){
        $hasSubmittedCity = FALSE;
        $errortext .= "<font color=\"red\">City not searched:</font> City is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedCity) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedState) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $state = str_replace(array('%','_'),'',$_GET['state']);
        $state = trim($state);

        if(ctype_space($state)){
        $hasSubmittedState = FALSE;
        $errortext .= "<font color=\"red\">State not searched:</font> Invalid state. Please enter a keyword. <br>";
        }

        if(strlen($state) < $namelength){
        $hasSubmittedState = FALSE;
        $errortext .= "<font color=\"red\">State not searched:</font> State is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedState) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedCountry) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $country = str_replace(array('%','_'),'',$_GET['country']);
        $country = trim($country);

        if(ctype_space($country)){
        $hasSubmittedCountry = FALSE;
        $errortext .= "<font color=\"red\">Country not searched:</font> Invalid country. Please enter a keyword. <br>";
        }

        if(strlen($country) < $namelength){
        $hasSubmittedCountry = FALSE;
        $errortext .= "<font color=\"red\">Country not searched:</font> Country is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedCountry) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      if($hasSubmittedZipcode) {
        //VALIDATE INPUT & get user search string for first/last name, store in variable here
        $zipcode = str_replace(array('%','_'),'',$_GET['zipcode']);
        $zipcode = trim($zipcode);

        if(ctype_space($zipcode)){
        $hasSubmittedZipcode = FALSE;
        $errortext .= "<font color=\"red\">Zipcode not searched:</font> Invalid zipcode. Please enter a keyword. <br>";
        }

        if(strlen($zipcode) < $namelength){
        $hasSubmittedZipcode = FALSE;
        $errortext .= "<font color=\"red\">Zipcode not searched:</font> Zipcode is too short. Please enter a keyword of at least " . $namelength . " characters. <br>";
        }

        if($hasSubmittedZipcode) //if there is real data here that will be used
          $datacount++; //add to counter when it is certain there is real data there
      }

      //NOTHING VALID GIVEN

      if($hasSubmittedFirstName == FALSE && $hasSubmittedLastName == FALSE && $hasSubmittedMiddleName == FALSE && $hasSubmittedSpecialty == FALSE && $hasSubmittedStreet == FALSE && $hasSubmittedCity == FALSE && $hasSubmittedState == FALSE && $hasSubmittedCountry == FALSE && $hasSubmittedZipcode == FALSE && strcmp($gender,"-") == 0 && strcmp($service_branch,"-") == 0 && strcmp($day,"--") == 0 && strcmp($month,"--") == 0 && strcmp($year,"----") == 0)
        $errortext .= "<br>Please enter at least one field to search from. <br>"; 

      ?>

      <form name="formSearch" method="get" action="search.php" display="table">
          <div class="container">

            <p display="table-row"><label>First name:</label>
            <input name="firstname" type="text" id="firstname" <?php if($hasSubmittedFirstName) echo ' value="'. $firstname .'"';?> >
            </p>

            <p display="table-row"><label>Middle name:</label>
            <input name="middlename" type="text" id="middlename" <?php if($hasSubmittedMiddleName) echo ' value="' . $middlename . '"';?> >
            </p>

            <p display="table-row"><label>Last name:</label>
            <input name="lastname" type="text" id="lastname" <?php if($hasSubmittedLastName) echo ' value="'. $lastname .'"';?> >
            </p>

            <p display="table-row"><label>Gender:</label>
            <select name="gender" id="gender">
              <option value="-" <?php if(ctype_space($gender) || $gender == "-") echo 'selected="true"'; ?> >-</option>
              <option value="M" <?php if(ctype_space($gender) || $gender == "M") echo 'selected="true"'; ?> >M</option>
              <option value="F" <?php if(ctype_space($gender) || $gender == "F") echo 'selected="true"'; ?> >F</option>
            </select>
            </p>

            <p display="table-row"><label>Specialty:</label>
            <input name="specialty" type="text" id="specialty" <?php if($hasSubmittedSpecialty) echo ' value="' . $specialty . '"';?> >
            </p>

            <p display="table-row"><label>Service Branch:</label>
            <select name="service_branch" id="service_branch">
              <option value="-" <?php if(ctype_space($service_branch) || $service_branch == "-") echo 'selected="true"'; ?> >-</option>
              <option value="Army" <?php if(ctype_space($service_branch) || $service_branch == "Army") echo 'selected="true"'; ?> >Army</option>
              <option value="Army (British)" <?php if(ctype_space($service_branch) || $service_branch == "Army (British)") echo 'selected="true"'; ?> >Army (British)</option>
              <option value="Coast Guard" <?php if(ctype_space($service_branch) || $service_branch == "Coast Guard") echo 'selected="true"'; ?> >Coast Guard</option>
              <option value="Harbor Defense" <?php if(ctype_space($service_branch) || $service_branch == "Harbor Defense") echo 'selected="true"'; ?> >Harbor Defense</option>
              <option value="Merchant Marines" <?php if(ctype_space($service_branch) || $service_branch == "Merchant Marines") echo 'selected="true"'; ?> >Merchant Marines</option>
              <option value="Navy" <?php if(ctype_space($service_branch) || $service_branch == "Navy") echo 'selected="true"'; ?> >Navy</option>
              <option value="Red Cross" <?php if(ctype_space($service_branch) || $service_branch == "Red Cross") echo 'selected="true"'; ?> >Red Cross</option>
              <option value="US Marine Corps" <?php if(ctype_space($service_branch) || $service_branch == "US Marine Corps") echo 'selected="true"'; ?> >US Marine Corps</option>
            </select>
            </p>

            <p display="table-row"><label><br></label></p>

            <p display="table-row"><label>Month:</label>
            <select name="month" id="month">
              <option value="--" <?php if(ctype_space($month) || $month == "--") echo 'selected="true"'; ?> >--</option>
              <option value="01" <?php if(ctype_space($month) || $month == "01") echo 'selected="true"'; ?> >January</option>
              <option value="02" <?php if(ctype_space($month) || $month == "02") echo 'selected="true"'; ?> >February</option>
              <option value="03" <?php if(ctype_space($month) || $month == "03") echo 'selected="true"'; ?> >March</option>
              <option value="04" <?php if(ctype_space($month) || $month == "04") echo 'selected="true"'; ?> >April</option>
              <option value="05" <?php if(ctype_space($month) || $month == "05") echo 'selected="true"'; ?> >May</option>
              <option value="06" <?php if(ctype_space($month) || $month == "06") echo 'selected="true"'; ?> >June</option>
              <option value="07" <?php if(ctype_space($month) || $month == "07") echo 'selected="true"'; ?> >July</option>
              <option value="08" <?php if(ctype_space($month) || $month == "08") echo 'selected="true"'; ?> >August</option>
              <option value="09" <?php if(ctype_space($month) || $month == "09") echo 'selected="true"'; ?> >September</option>
              <option value="10" <?php if(ctype_space($month) || $month == "10") echo 'selected="true"'; ?> >October</option>
              <option value="11" <?php if(ctype_space($month) || $month == "11") echo 'selected="true"'; ?> >November</option>
              <option value="12" <?php if(ctype_space($month) || $month == "12") echo 'selected="true"'; ?> >December</option>
            </select>
            </p>

            <p display="table-row"><label>Day:</label>
            <select name="day" id="day">
              <option value="--" <?php if(ctype_space($day) || $day == "--") echo 'selected="true"'; ?> >--</option>
              <option value="01" <?php if(ctype_space($day) || $day == "01") echo 'selected="true"'; ?> >01</option>
              <option value="02" <?php if(ctype_space($day) || $day == "02") echo 'selected="true"'; ?> >02</option>
              <option value="03" <?php if(ctype_space($day) || $day == "03") echo 'selected="true"'; ?> >03</option>
              <option value="04" <?php if(ctype_space($day) || $day == "04") echo 'selected="true"'; ?> >04</option>
              <option value="05" <?php if(ctype_space($day) || $day == "05") echo 'selected="true"'; ?> >05</option>
              <option value="06" <?php if(ctype_space($day) || $day == "06") echo 'selected="true"'; ?> >06</option>
              <option value="07" <?php if(ctype_space($day) || $day == "07") echo 'selected="true"'; ?> >07</option>
              <option value="08" <?php if(ctype_space($day) || $day == "08") echo 'selected="true"'; ?> >08</option>
              <option value="09" <?php if(ctype_space($day) || $day == "09") echo 'selected="true"'; ?> >09</option>
              <option value="10" <?php if(ctype_space($day) || $day == "10") echo 'selected="true"'; ?> >10</option>
              <option value="11" <?php if(ctype_space($day) || $day == "11") echo 'selected="true"'; ?> >11</option>
              <option value="12" <?php if(ctype_space($day) || $day == "12") echo 'selected="true"'; ?> >12</option>
              <option value="13" <?php if(ctype_space($day) || $day == "13") echo 'selected="true"'; ?> >13</option>
              <option value="14" <?php if(ctype_space($day) || $day == "14") echo 'selected="true"'; ?> >14</option>
              <option value="15" <?php if(ctype_space($day) || $day == "15") echo 'selected="true"'; ?> >15</option>
              <option value="16" <?php if(ctype_space($day) || $day == "16") echo 'selected="true"'; ?> >16</option>
              <option value="17" <?php if(ctype_space($day) || $day == "17") echo 'selected="true"'; ?> >17</option>
              <option value="18" <?php if(ctype_space($day) || $day == "18") echo 'selected="true"'; ?> >18</option>
              <option value="19" <?php if(ctype_space($day) || $day == "19") echo 'selected="true"'; ?> >19</option>
              <option value="20" <?php if(ctype_space($day) || $day == "20") echo 'selected="true"'; ?> >20</option>
              <option value="21" <?php if(ctype_space($day) || $day == "21") echo 'selected="true"'; ?> >21</option>
              <option value="22" <?php if(ctype_space($day) || $day == "22") echo 'selected="true"'; ?> >22</option>
              <option value="23" <?php if(ctype_space($day) || $day == "23") echo 'selected="true"'; ?> >23</option>
              <option value="24" <?php if(ctype_space($day) || $day == "24") echo 'selected="true"'; ?> >24</option>
              <option value="25" <?php if(ctype_space($day) || $day == "25") echo 'selected="true"'; ?> >25</option>
              <option value="26" <?php if(ctype_space($day) || $day == "26") echo 'selected="true"'; ?> >26</option>
              <option value="27" <?php if(ctype_space($day) || $day == "27") echo 'selected="true"'; ?> >27</option>
              <option value="28" <?php if(ctype_space($day) || $day == "28") echo 'selected="true"'; ?> >28</option>
              <option value="29" <?php if(ctype_space($day) || $day == "29") echo 'selected="true"'; ?> >29</option>
              <option value="30" <?php if(ctype_space($day) || $day == "30") echo 'selected="true"'; ?> >30</option>
              <option value="31" <?php if(ctype_space($day) || $day == "31") echo 'selected="true"'; ?> >31</option>
            </select>
            </p>

            <p display="table-row"><label>Year:</label>
            <select name="year" id="year">
              <option value="----" <?php if(ctype_space($year) || $year == "----") echo 'selected="true"'; ?> >----</option>
              <option value="1941" <?php if(ctype_space($year) || $year == "1941") echo 'selected="true"'; ?> >1941</option>
              <option value="1942" <?php if(ctype_space($year) || $year == "1942") echo 'selected="true"'; ?> >1942</option>
              <option value="1943" <?php if(ctype_space($year) || $year == "1943") echo 'selected="true"'; ?> >1943</option>
              <option value="1944" <?php if(ctype_space($year) || $year == "1944") echo 'selected="true"'; ?> >1944</option>
              <option value="1945" <?php if(ctype_space($year) || $year == "1945") echo 'selected="true"'; ?> >1945</option>
              <option value="1946" <?php if(ctype_space($year) || $year == "1946") echo 'selected="true"'; ?> >1946</option>
              <option value="1950" <?php if(ctype_space($year) || $year == "1950") echo 'selected="true"'; ?> >1950</option>
            </select>
            </p>

            <p display="table-row"><label><br></label></p>

            <p display="table-row"><label>Street:</label>
            <input name="street" type="text" id="street" <?php if($hasSubmittedStreet) echo ' value="'. $street .'"';?> >
            </p>

            <p display="table-row"><label>City:</label>
            <input name="city" type="text" id="city" <?php if($hasSubmittedCity) echo ' value="'. $city .'"';?> >
            </p>

            <p display="table-row"><label>State:</label>
            <input name="state" type="text" id="state" <?php if($hasSubmittedState) echo ' value="'. $state .'"';?> >
            </p>

            <p display="table-row"><label>Country:</label>
            <input name="country" type="text" id="country" <?php if($hasSubmittedCountry) echo ' value="'. $country .'"';?> >
            </p>

            <p display="table-row"><label>Zipcode:</label>
            <input name="zipcode" type="text" id="zipcode" <?php if($hasSubmittedZipcode) echo ' value="'. $zipcode .'"';?> >
            </p>

            <p display="table-row"><label><br></label></p>

            <p display="table-row"><label>Sort results:</label>
            <select name="OrderType" id="OrderType">
              <option value="fname" <?php if(ctype_space($orderType) || $orderType == "fname") echo 'selected="true"'; ?> >By First Name</option>
              <option value="lname" <?php if(ctype_space($orderType) || $orderType == "lname") echo 'selected="true"'; ?> >By Last Name</option>
              <option value="chrono" <?php if(ctype_space($orderType) || $orderType == "chrono") echo 'selected="true"'; ?> >Chronologically</option>
            </select>
            </p>

            <p display="table-row"><label><br></label></p>

            <p display="table-row"><label></label>
            <input type="submit" value="Search">
            </p>
          </div>
      </form>

      <?php 
      //Putting together the query string...

      if(strcmp($day,"--") != 0) {
        if($datacount != 1) {
          $querystring .= ' DAY(ts_dateguess) = :day AND';
          $datacount--;
        }

        else {
          $querystring .= ' DAY(ts_dateguess) = :day';
        }
      }

      if(strcmp($month,"--") != 0) {
        if($datacount != 1) {
          $querystring .= ' MONTH(ts_dateguess) = :month AND';
          $datacount--;
        }

        else {
          $querystring .= ' MONTH(ts_dateguess) = :month';
        }
      }

      if(strcmp($year,"----") != 0) {
        if($datacount != 1) {
          $querystring .= ' YEAR(ts_dateguess) = :year AND';
          $datacount--;
        }

        else {
          $querystring .= ' YEAR(ts_dateguess) = :year';
        }
      }

      //FIRST NAME EXISTS AS VALID DATA

      if($hasSubmittedFirstName) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' firstname like :firstname AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' firstname like :firstname';
          }
      }

      //LAST NAME EXISTS AS VALID DATA

      if($hasSubmittedLastName) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' lastname like :lastname AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' lastname like :lastname';
          }
      }

      //MIDDLE NAME EXISTS AS VALID DATA

      if($hasSubmittedMiddleName) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' middlename like :middlename AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' middlename like :middlename';
          }
      }

      //GENDER EXISTS AS VALID DATA

      if(strcmp($gender,"-") != 0) {
        if($datacount != 1) {
          $querystring .= ' gender = :gender AND';
          $datacount--;
        }

        else {
          $querystring .= ' gender = :gender';
        }
      }

      //SPECIALTY EXISTS AS VALID DATA

      if($hasSubmittedSpecialty) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' specialty like :specialty AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' specialty like :specialty';
          }
      }

      //SERVICE BRANCH EXISTS AS VALID DATA

      if(strcmp($service_branch,"-") != 0) {
        if($datacount != 1) {
          $querystring .= ' service_branch = :service_branch AND';
          $datacount--;
        }

        else {
          $querystring .= ' service_branch = :service_branch';
        }
      }

      //STREET EXISTS AS VALID DATA

      if($hasSubmittedStreet) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' location_street like :street AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' location_street like :street';
          }
      }

      //CITY EXISTS AS VALID DATA

      if($hasSubmittedCity) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' location_city like :city AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' location_city like :city';
          }
      }

      //STATE EXISTS AS VALID DATA

      if($hasSubmittedState) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' location_state like :state AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' location_state like :state';
          }
      }

      //COUNTRY EXISTS AS VALID DATA

      if($hasSubmittedCountry) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' location_country like :country AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' location_country like :country';
          }
      }

      //ZIPCODE EXISTS AS VALID DATA

      if($hasSubmittedZipcode) {
          if($datacount != 1) { //not the very last piece of data
            $querystring .= ' location_zipcode like :zipcode AND';
            $datacount--;
          }

          else { //the very last piece of data
            $querystring .= ' location_zipcode like :zipcode';
          }
      }

      //This is needed to limit results to one record per find, since the search is against more than one table.

      $querystring .= ' AND letters.id = locdata.locationid';

      if($orderType == "fname")
        $querystring .= ' ORDER BY firstname';

      if($orderType == "lname")
        $querystring .= ' ORDER BY lastname';

      if($orderType == "chrono")
        $querystring .= ' ORDER by ts_dateguess';

      ?>

      <?php 

        include 'dbinfo.php';

      	try {

            /****************************
            * 
            * For DB in this web application part of the WW2 Letters project,
            * we're going to use the PHP Data Objects (PDO) library
            * Documentation on PDO: http://www.php.net/manual/en/book.pdo.php
            *
            ****************************/

            //open the mysql connection using a PDO interface object
            $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $pass);

             //if no piece of valid data exists, just print the error messages

             if($hasSubmittedFirstName == FALSE && $hasSubmittedLastName == FALSE && $hasSubmittedMiddleName == FALSE && $hasSubmittedSpecialty == FALSE && $hasSubmittedStreet == FALSE && $hasSubmittedCity == FALSE && $hasSubmittedState == FALSE && $hasSubmittedCountry == FALSE && $hasSubmittedZipcode == FALSE && strcmp($gender,"-") == 0 && strcmp($service_branch,"-") == 0 && strcmp($day,"--") == 0 && strcmp($month,"--") == 0 && strcmp($year,"----") == 0) {

                echo '<p class="searchTermBox">You Entered: <br>';
                echo '<br>';
                echo 'First name: ' . $firstname;
                echo '<br>';
                echo 'Middle name: ' . $middlename;
                echo '<br>';
                echo 'Last name: ' . $lastname;
                echo '<br>';
                echo 'Gender: ' . $gender;
                echo '<br>';
                echo 'Specialty: ' . $specialty;
                echo '<br>';
                echo 'Service Branch: ' . $service_branch;
                echo '<br>';
                echo '<br>';
                echo 'Month: ' . $months[$month];
                echo '<br>';
                echo 'Day: ' . $day;
                echo '<br>';
                echo 'Year: ' . $year;
                echo '<br>';
                echo '<br>';
                echo 'Street: ' . $street;
                echo '<br>';
                echo 'City: ' . $city;
                echo '<br>';
                echo 'State: ' . $state;
                echo '<br>';
                echo 'Country: ' . $country;
                echo '<br>';
                echo 'Zipcode: ' . $zipcode . '</p>';


                echo '<p class="bold">' . $errortext . "</p>";
             }

             //if any piece of valid datum exists, do search

             if($hasSubmittedFirstName || $hasSubmittedLastName || $hasSubmittedMiddleName || $hasSubmittedSpecialty || $hasSubmittedStreet || $hasSubmittedCity|| $hasSubmittedState || $hasSubmittedCountry || $hasSubmittedZipcode || strcmp($gender,"-") != 0 || strcmp($service_branch,"-") != 0 || strcmp($day,"--") != 0 || strcmp($month,"--") != 0 || strcmp($year,"----") != 0) {

                echo '<p class="searchTermBox">You Entered: <br>';
                echo '<br>';
                echo 'First name: ' . $firstname;
                echo '<br>';
                echo 'Middle name: ' . $middlename;
                echo '<br>';
                echo 'Last name: ' . $lastname;
                echo '<br>';
                echo 'Gender: ' . $gender;
                echo '<br>';
                echo 'Specialty: ' . $specialty;
                echo '<br>';
                echo 'Service Branch: ' . $service_branch;
                echo '<br>';
                echo '<br>';
                echo 'Month: ' . $months[$month];
                echo '<br>';
                echo 'Day: ' . $day;
                echo '<br>';
                echo 'Year: ' . $year;
                echo '<br>';
                echo '<br>';
                echo 'Street: ' . $street;
                echo '<br>';
                echo 'City: ' . $city;
                echo '<br>';
                echo 'State: ' . $state;
                echo '<br>';
                echo 'Country: ' . $country;
                echo '<br>';
                echo 'Zipcode: ' . $zipcode . '</p>';

              //TODO:   drop down to select search types in the HTML form

               //echo '<p class="bold">' . $querystring ."</p>";
               //echo var_dump($month);
               echo '<p class="bold">' . $errortext . "</p>";

               //this query will return any partial matches from user string
               //for example, the string "ack" will return ack, ackerman, etc
               $query = $dbh->prepare($querystring);
             
               if ($hasSubmittedFirstName) {             
                  $firstname = "%".$firstname."%"; //add wildcards to original user string for search
                  $query->bindParam(':firstname', $firstname, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedLastName) {
                  $lastname = "%".$lastname."%"; //add wildcards to original user string for search
                  $query->bindParam(':lastname', $lastname, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedMiddleName) {
                  $middlename = "%".$middlename."%"; //add wildcards to original user string for search
                  $query->bindParam(':middlename', $middlename, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedSpecialty) {
                  $specialty = "%".$specialty."%"; //add wildcards to original user string for search
                  $query->bindParam(':specialty', $specialty, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedStreet) {
                  $street = "%".$street."%"; //add wildcards to original user string for search
                  $query->bindParam(':street', $street, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedCity) {
                  $city = "%".$city."%"; //add wildcards to original user string for search
                  $query->bindParam(':city', $city, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedState) {
                  $state = "%".$state."%"; //add wildcards to original user string for search
                  $query->bindParam(':state', $state, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedCountry) {
                  $country = "%".$country."%"; //add wildcards to original user string for search
                  $query->bindParam(':country', $country, PDO::PARAM_STR); //bind string to reference by query
               }

               if ($hasSubmittedZipcode) {
                  $zipcode = "%".$zipcode."%"; //add wildcards to original user string for search
                  $query->bindParam(':zipcode', $zipcode, PDO::PARAM_STR); //bind string to reference by query
               }

               if (strcmp($gender,"-") != 0) {
                  $query->bindParam(':gender', $gender, PDO::PARAM_STR);
               }

               if (strcmp($service_branch,"-") != 0) {
                  $query->bindParam(':service_branch', $service_branch, PDO::PARAM_STR);
               }

               if (strcmp($day,"--") != 0) {
                  $query->bindParam(':day', $day, PDO::PARAM_STR);
               }

               if (strcmp($month,"--") != 0) {
                  $query->bindParam(':month', $month, PDO::PARAM_STR);
               }

               if (strcmp($year,"----") != 0) {
                  $query->bindParam(':year', $year, PDO::PARAM_STR);
               }

               $query->execute();

               //print_r($results);
               $numResults = $query->rowCount();
               echo '<p class="bold">' . $numResults . ' Results Found</p>';
               if($numResults == 0){
                 echo "Please try again.";
               }

               while($results = $query->fetch()) {
                echo '<p class="resultRow"><span class="name"><a href="viewauthor.php?firstname=' . urlencode($results['firstname']) . '&lastname=' . urlencode($results['lastname']) .'">' . $results['lastname'] . ', ' . $results['firstname'] . '</a>:</span><span class="filename"><a href="viewletter.php?letterid=' . $results['id'] . '">' .$results['filename']. '</a></p>';
                //print_r($results);
               }

               $dbh = null; //connection closed
              }

         } catch (PDOException $e) {
             print "Error!: " . $e->getMessage() . "<br/>";
             die();
         }

      ?>

      <!-- <p>TODO: expand search fields (pending more data)<br>
         TODO: make the viewletters line up uniformly<br>
         TODO: modularize work into functions? (see viewauthor.php)
      </p> -->
   </body>

</html>
