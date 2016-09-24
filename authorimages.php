<?php

  include 'dbinfo.php';

  //open the mysql connection using a PDO interface object
  $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $pass);

  $lastname = NULL;
  $firstname = NULL;
  $imgnum = NULL;
  
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

  function printImageThumbnails($firstname,$lastname,$results,$imgnum) {

  	$total_image_array = array(); //empty array
    $total_filename_array = array();

  	foreach($results as $row) {

  		$imagename = $row['filename'] . "*.jp*";
  		$imagepathname = "images/" . $imagename; //path ready
      array_push($total_filename_array,$row['filename']); //store all the db filenames to be able to show img progress by letter later on

  		$images = glob($imagepathname); //find all images with this filename "root"
  		$total_image_array = array_merge($total_image_array,$images); //merge this new array into the larger one containing all the image filenames
  	}

  	if(count($total_image_array) > 0) { //if there were some images at all

  		$length = count($total_image_array);
      $prevlink = NULL;
      $nextlink = NULL;

  		if($imgnum < 1 || $imgnum > $length) { //if the number is not within [1,max # of images], inclusive on both bounds, kill the page

  			echo '<p>You seem to have requested an image not on our records. Please <a href="index.php">browse the index</a> to find an author you would like to view.</p>'; 
      		die(); 
  		}

      $prev = $imgnum-1;
      $next = $imgnum+1;

  		if($imgnum == 1) { //want the first image in the set, need to wrap the prev link around

        $prevlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $length;

        if($length > 1) //if there are 2 or more images total in the set, next works as expected (current+1)
          $nextlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $next;

        if($length == 1) //however, if there is only 1 image total in the set, both the prev and next links should point to that one image
          $nextlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $length;
  		}

  		else if($imgnum == $length) { //want the last image in the set, need to wrap the next link around

        $prevlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $prev;
        $nextlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . 1;
  		}

  		else { //just some img in the middle of the set, no special wrapping needed

        $prevlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $prev;
        $nextlink = 'authorimages.php?firstname=' . $firstname . '&lastname=' . $lastname . '&imgnum=' . $next;
  		}

      $prevlink = htmlentities($prevlink, ENT_QUOTES, "UTF-8");
      $nextlink = htmlentities($nextlink, ENT_QUOTES, "UTF-8");

  		$requestedimage = $total_image_array[$imgnum-1]; //-1 because arrays 0 offset, and 1-x is used for the URL
  		$requestedparts = explode("/",$requestedimage); //separate on dir

	      $thumbs = "thumbs/" . $requestedparts[1]; //gives thumbs/filename.jpg
        $highres = "images/" . $requestedparts[1]; //gives images/filename.jpg

        $thumbs = htmlentities($thumbs, ENT_QUOTES, "UTF-8");
        $highres = htmlentities($highres, ENT_QUOTES, "UTF-8");

        $thumbstring= '<img src="' . $thumbs . '">';
        $finalurl = '<a href="' . $highres . '" class="centerImage">' . $thumbstring . '</a>';

        echo "<h3>Image: " . $imgnum . "/" . $length . "</h2>";

        $filename = explode(".",$requestedparts[1]);

        //filename[0] is the jpg filename, filename[1] is the jpg extension itself

        $letterprogress = array();

        foreach($total_filename_array as $testname) {

            if(count($letterprogress) != 2) { //try exploding until the explode is actually successful

            $letterprogress = explode($testname, $filename[0]);

            }
        }

        //echo "<h3>Filename: " . $letterprogress[1]. "</h3><br>";

        if($letterprogress[1]) { //if the filename indiciates there is more than one image per letter, write out the progress on the images accordingly

          $displayname = explode($letterprogress[1],$filename[0]);

          echo "<h3>Letter name: " . $displayname[0] . "</h3>";

          echo "<h3>Progress for this letter: " . str_replace("-","/",$letterprogress[1]) . "</h3>";
        }

        else { //the filename didn't indicate multiple images on the letter

          echo "<h3>Letter name: " . $filename[0] . "</h3>";

          echo "<h3>Progress for this letter: 1/1  </h3>";

        }

        echo '<a href="' . $prevlink . '" class="prevArrow"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="font-size:75px"></span></a>';
        echo '<a href="' . $nextlink . '" class="nextArrow"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true" style="font-size:75px"></span></a>';
        echo '<div style="clear:both;"></div>';
        echo '<a href="' . $prevlink . '" class="prevArrowLower"><span class="glyphicon glyphicon-arrow-left" aria-hidden="true" style="font-size:75px"></span></a>';
        echo '<a href="' . $nextlink . '" class="nextArrowLower"><span class="glyphicon glyphicon-arrow-right" aria-hidden="true" style="font-size:75px"></span></a>';
        echo $finalurl;
        echo '<div style="position: relative; bottom: 0px; text-align:center; margin-top:50px">Icon set courtesy of <a href="http://glyphicons.com/">Glyphicons</a></div>';
  	}

  	else { //the image array is empty, so there were no images

  		echo "Image scans are not available at this time. <br>";
  		die();
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
      .panel-heading {cursor:pointer;}
      .prevArrow {
        float:left;
      }
      .nextArrow {
        float:right;
      }
      .prevArrowLower {
        float:left;
        margin-top:400px;
      }
      .nextArrowLower {
        float:right;
        margin-top:400px;
      }
      .centerImage {
        margin:0 auto;
        display:table;
      }
    </style>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">

    <?php

  	if((isset($_GET['imgnum']) && !empty($_GET['imgnum']) && is_numeric($_GET['imgnum']))) {

  		$imgnum = $_GET['imgnum'];
  	}

  	else {

  		include 'menu.php';
    	echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to find an author you would like to view.</p>'; 
    	die(); //halts further page processing, similar to System.exit()
  	}

    setQueryVars($firstname,$lastname);

    ?>

    <meta charset="UTF-8">
    <title>

     <?php echo 'Author Images: ' . $firstname . ' ' . $lastname; ?>

    </title>

   <!-- UI stuff -->
   <script type="text/javascript" src="jquery/jquery-2.1.1.js"></script>
   <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>

</head>

<body>

<?php

include 'menuauthorimages.php';

$results = doQuery($firstname,$lastname,$dbh);

echo '<h1>Author Images: ' . $firstname . ' ' . $lastname . '</h1>'; 

printImageThumbnails($firstname,$lastname,$results,$imgnum);
?>

</body>

</html>