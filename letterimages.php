<?php

  include 'dbinfo.php';

  //open the mysql connection using a PDO interface object
  $dbh = new PDO('mysql:host=' . $host . ';dbname=' . $dbname, $user, $pass);

  $LID = NULL;
  
  function setQueryVars(&$LID) {

      $LID = -1;

      if(isset($_GET['letterid']) && !empty($_GET['letterid']) && is_numeric($_GET['letterid'])){
        $LID = $_GET['letterid'];
      }

      else {
        include 'menu.php';
        echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to find a letter you would like to view.</p>';

        die();
      }

    }

  function doQuery($LID,$dbh) {

        if($LID > 0){

        //check if LID exists in DB
        $checkLID = $dbh->prepare('SELECT * FROM letters WHERE id = :lid');
        $checkLID->bindValue(':lid',$LID,PDO::PARAM_INT);
        $checkLID->execute();

          if($checkLID->rowCount() == 0) { 
            $LID = -1; 
          }
        }

        if($LID <= 0){
        //force them to redirect
        //header("Location: search.php");

        include 'menu.php';
        //or give them a message with a link
        echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to find a letter you would like to view.</p>';
      
        die(); //halts further page processing, similar to System.exit()
        }

        else {

          //ret results when LID > 0 AND rowCount() != 0
          $results = $checkLID->fetch();
          return $results;
        }

    }

  function printImageThumbnails($LID,$results,$imgnum) {

  	  $total_image_array = array(); //empty array

  		$imagename = $results['filename'] . "*.jp*";

  		$imagepathname = "images/" . $imagename; //path ready

  		$images = glob($imagepathname); //find all images with this filename "root"
  		$total_image_array = array_merge($total_image_array,$images); //merge this new array into the larger one containing all the image filenames

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

        $prevlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $length;

        if($length > 1) //if there are 2 or more images total in the set, next works as expected (current+1)
        $nextlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $next;

        if($length == 1) //however, if there is only 1 image total in the set, both the prev and next links should point to that one image
        $nextlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $length;
  		}

  		else if($imgnum == $length) { //want the last image in the set, need to wrap the next link around

        $prevlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $prev;
        $nextlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . 1;
  		}

  		else { //just some img in the middle of the set, no special wrapping needed

        $prevlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $prev;
        $nextlink = 'letterimages.php?letterid=' . $LID . '&imgnum=' . $next;
  		}

      $prevlink = htmlentities($prevlink, ENT_QUOTES, "UTF-8");
      $nextlink = htmlentities($nextlink, ENT_QUOTES, "UTF-8");

  		$requestedimage = $total_image_array[$imgnum-1]; //-1 because arrays 0 offset, and 1 to x is used for the URL
  		$requestedparts = explode("/",$requestedimage); //separate on dir

	      $thumbs = "thumbs/" . $requestedparts[1]; //gives thumbs/filename.jpg
        $highres = "images/" . $requestedparts[1]; //gives images/filename.jpg

        $thumbs = htmlentities($thumbs, ENT_QUOTES, "UTF-8");
        $highres = htmlentities($highres, ENT_QUOTES, "UTF-8");

        $thumbstring= '<img src="' . $thumbs . '">';
        $finalurl = '<a href="' . $highres . '" class="centerImage">' . $thumbstring . '</a>';

        echo "<h3>Image: " . $imgnum . "/" . $length . "</h2>";

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

    setQueryVars($LID);

    $results = doQuery($LID,$dbh);

    ?>

    <meta charset="UTF-8">
    <title>

     <?php echo 'Letter Images: ' . $results['filename']; ?>

    </title>

   <!-- UI stuff -->
   <script type="text/javascript" src="jquery/jquery-2.1.1.js"></script>
   <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>

</head>

<body>

<?php

include 'menuletterimages.php';

echo '<h1>Letter Images: ' . $results['filename'] . '</h1>'; 

printImageThumbnails($LID,$results,$imgnum);

?>

</body>

</html>