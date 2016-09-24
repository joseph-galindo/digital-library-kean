<?php

  /*IMPORTANT GLOBALS*/

  include 'dbinfo.php';

  //open the mysql connection using a PDO interface object
  $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $pass);

  $lastname = NULL;
  $firstname = NULL;
  //$scriptname = $_SERVER['PHP_SELF'];


  function setQueryVars(&$firstname,&$lastname) {

    if((isset($_GET['firstname']) && !empty($_GET['firstname'])) || (isset($_GET['lastname']) && !empty($_GET['lastname']))) {
    
      //got a firstname and a lastname
      $firstname = $_GET['firstname'];
      $lastname = $_GET['lastname'];
    }

    else {
      
      //got neither a firstname or lastname, cannot create page
      include 'menu.php';
      echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to find an author you would like to view.</p>'; 
      die(); //halts further page processing, similar to System.exit()
    }

  }

  function doQuery($firstname,$lastname,$dbh) {

    $query = $dbh->prepare('SELECT * FROM letters WHERE firstname LIKE :firstname AND lastname LIKE :lastname ORDER BY ts_dateguess');

    //todo: change the query to do straight comparisons, instead of LIKE comparisons? to avoid cases like firstname=a&lastname=b actually working

    $firstname = "%".$firstname."%"; //add wildcards to original user string for search
    $query->bindParam(':firstname', $firstname, PDO::PARAM_STR); //bind string to reference by query

    $lastname = "%".$lastname."%"; //add wildcards to original user string for search
    $query->bindParam(':lastname', $lastname, PDO::PARAM_STR); //bind string to reference by query
    //$query->debugDumpParams();

    $query->execute();

    //this case CAN happen; for example, viewauthor.php?firstname=Trumpet&lastname=Tuba 
    //above string would get through all the checks, and just print Letters:, Images:, Mapping: with no content
    if($query->rowCount() == 0) {

      echo '<p>You seem to have provided an author not on our records. Please <a href="index.php">browse the index</a> to find an author you would like to view.</p>'; 
      die(); 
    }

    else {

      //get all the results in an array that can be traversed as many times as needed
      $results = $query -> fetchAll(); 
      return $results;
    }

  }

  function printLetterLinks($results) {

      foreach($results as $row) {
          echo '<p class="row"><a href="viewletter.php?letterid=' . $row['id'] . '">' .$row['filename']. '</a></p>';
    }
  }

  function printImageThumbnails($results, $firstname, $lastname) {

   //strip the % wildcards added (below) to these vars during the query
   //no longer needed because these vars no longer have %
   //because they were passed-by-value to the func that added the %
   //
   //$firstname = str_replace(array('%'),'',$firstname);
   //$lastname = str_replace(array('%'),'',$lastname);

   $imgcount = 0;

   foreach($results as $row) {

      $imagename = $row['filename'] . "*.jp*";
      $imagepathname = "images/" . $imagename; //complete path ready to be used!

      $images = glob($imagepathname); //grab all the images associated with this particular author

      if($images) {

          foreach($images as $var) {  //this whole loop innard has to change when the images are actually on server
            //$var = rawurlencode($var); //ONLY WORKS LIKE THIS IF AllowEncodedSlashes On in apache httpd.conf
            //$var = str_replace("%2F","/",$var);

            $imgcount++;

	          $string_array = explode("/",$var); //separate the url on the directory

	          $thumbs = "thumbs/" . $string_array[1]; //gives thumbs/filename.jpg
            $highres = "images/" . $string_array[1]; //gives images/filename.jpg

            $thumbs = htmlentities($thumbs, ENT_QUOTES, "UTF-8");
            $highres = htmlentities($highres, ENT_QUOTES, "UTF-8");

            $thumbstring= '<img src="' . $thumbs . '" >';

            if($imgcount === 1) {

              echo '<div id="carousel" class="carousel slide" style="width:750px !important;">';

              //indicators
              echo '<ol class="carousel-indicators"></ol>';

              //carousel inner class
              echo '<div class="carousel-inner" role="listbox" style="width:750px !important;">';

              echo '<div class="item active" style="max-height:400px;max-width:750px;">';
            }
            else
              echo '<div class="item" style="max-height:400px;max-width:750px;">';

                echo $thumbstring;
                echo '<div class="carousel-caption" style="margin: 0 auto; height:200px;">';
                  echo '<a href="' . 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $imgcount . '">' . $row['filename'] . '</a>';
                  //echo '<a href="' . $highres . '">' . $row['filename'] . '</a>';//href on caption only!
                  //echo '<a href=' . $var . '>';//href on image, not caption
                echo '</div>';
              echo '</div>';
          }

          //all the items added to carousel, break carousel inner div
      }
    }

    if($imgcount !== 0) {
          echo '</div>'; //end of the carousel-inner

          echo '<a class="left carousel-control" href="#carousel" role="button" data-slide="prev">';
            echo '<span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>';
            echo '<span class="sr-only">Previous</span>';
            echo '</a>';
          echo '<a class="right carousel-control" href="#carousel" role="button" data-slide="next">';
            echo '<span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>';
            echo '<span class="sr-only">Next</span>';
            echo '</a>';

          echo '</div>';  //end of the carousel
    }

    if($imgcount === 0) { //if we visited each person and not one letter was found

      echo "Image scans are not available at this time. <br>";
    }
  }

?>

<html>

   <head>

    <style type="text/css">
      body{ height: 100%; font-family: "Lucida Sans", Trebuchet, monospace; }
      .searchTermBox{ border:solid 1px #333; font-weight:bold; color:#333; padding: 5px;}
      .bold{ font-weight:bold; }
      .row{ border-bottom:1px solid #ccc; }
      .alt{ background-color: #eee; }
      a:visited { color: #800080;}
      .letterLink,.filename{margin-left:20px;}
      .gm-style {
        -webkit-box-sizing: content-box;
        -moz-box-sizing: content-box;
        box-sizing: content-box;
      }
      #map {height: 75%; width: 750px; position: relative;}
      #maperror {height: 3%;}
      #legend { background:white; padding:10px; display:none;}
      .panel-heading {cursor:pointer;}
    </style>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">

    <?php

    setQueryVars($firstname,$lastname); //do this here so body onload has the vars to use

    ?>

    <meta charset="UTF-8">
    <title>

    <?php

    echo 'Author: ' . $firstname . ' ' . $lastname;

    $jsfirstname = htmlentities($firstname, ENT_QUOTES, "UTF-8");
    $jslastname = htmlentities($lastname, ENT_QUOTES, "UTF-8");

    ?>
    </title>

   <!-- Map scripts/resources -->
   <script type="text/javascript" src="http://maps.googleapis.com/maps/api/js?libraries=geometry"></script>
   <script type="text/javascript" src="map_libraries/OverlappingMarkerSpiderfier.js"></script>
   <script type="text/javascript" src="map_libraries/markerwithlabel.js"></script>
   <script type="text/javascript" src="map_libraries/polyline_labels.js"></script>

   <!-- UI stuff -->
   <script type="text/javascript" src="jquery/jquery-2.1.1.js"></script>
   <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>

   <!-- Custom map script -->
   <script type="text/javascript" src="map_js/map_author.js"></script>
   <script type="text/javascript" src="map_js/create_legend.js"></script>

   </head>

   <body onload="load(&#34;<?php echo $jsfirstname; ?>&#34;, &#34;<?php echo $jslastname; ?>&#34;)">

   <?php

   //maybe some sort of bio info from the history dept guys?
   //if it's a .txt file could be easily imported and printed here

   include 'menu.php';

   $results = doQuery($firstname,$lastname,$dbh);

   echo '<h1>View Author: ' . $firstname . ' ' . $lastname . '</h1>'; ?>

   <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">

    <div class="panel panel-default" style="width:775px">
      <div class="panel-heading" role="tab" id="ViewLetters" data-toggle="collapse" data-parent="#accordion" data-target="#collapseViewLetters">
        <h2 class="panel-title">
          <a class="accordion-toggle">

            Letters

          </a>
        </h2>
      </div>
      <div id="collapseViewLetters" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ViewLetters">
        <div class="panel-body"> 

  <?php printLetterLinks($results); ?>

        </div>
      </div>
    </div>
  
    <div class="panel panel-default" style="width:775px">
      <div class="panel-heading" role="tab" id="ViewImages" data-toggle="collapse" data-parent="#accordion" data-target="#collapseViewImages">
        <h2 class="panel-title">
          <a class="accordion-toggle">

            Images

          </a>
        </h2>
      </div>
      <div id="collapseViewImages" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ViewImages">
        <div class="panel-body">

  <?php printImageThumbnails($results, $firstname, $lastname); ?>

        </div>
      </div>
    </div>

    <div class="panel panel-default" style="width:775px">
      <div class="panel-heading" role="tab" id="ViewMap" data-toggle="collapse" data-parent="#accordion" data-target="#collapseViewMap">
        <h2 class="panel-title">
          <a class="accordion-toggle">

            Mapping

          </a>
        </h2>
      </div>
      <div id="collapseViewMap" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ViewMap">
        <div class="panel-body">

          <div id="maperror"></div>
          <div id="map"></div>
          <div id="legend"><h3>Legend</h3></div>

          <div id="marker_traveller">
              <div id="traversal"></div><br>
              <button onclick="goto_first_marker()">First Letter</button>
              <button onclick="goto_final_marker()">Final Letter</button><br><br>
              <button onclick="goto_previous_marker()">Previous Letter</button>
              <button onclick="goto_next_marker()">Next Letter</button><br><br>
              <button onclick="reset_map_view()">Reset Map View</button>
              <button onclick="toggle_legend_visibility()">Toggle Legend Visibility</button>
          </div>

        </div>
      </div>
    </div>
  </div>

</body>

<!-- <p>IMPORTANT NOTE: AllowEncodedSlashes On *must* be set in the Apache httpd.conf for correct image loading.</p> -->

</html>
