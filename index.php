<html>

 <head>

    <meta charset="UTF-8">
    <title>Nancy Thompson Library: Index Page</title>

    <style type="text/css">
      body{ font-family: "Lucida Sans", Trebuchet, monospace; }
      .searchTermBox{ border:solid 1px #333; font-weight:bold; color:#333; padding: 5px;}
      .bold{ font-weight:bold; }
      .resultRow{ border-bottom:1px solid #ccc; }
      .alt{ background-color: #eee; }
      a:visited { color: #800080;}
      .panel-heading {cursor:pointer;}
    </style>

   <!-- UI stuff -->
   <script type="text/javascript" src="jquery/jquery-2.1.1.js"></script>
   <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
   <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">

   </head>

   <body>

      <?php include 'menu.php'; ?>
      <h1>Table of Contents</h1>
      <h3>Last Name, First Name, # of Letters</h3>   

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

	           $pdostring = 'mysql:host=' . $host . ';dbname=' . $dbname;

             //open the mysql connection using a PDO interface object
             $dbh = new PDO($pdostring, $user, $pass);
             
             //VERY ROUGH output of Query Array, first 10 rows of DB

             //thinking of making each toc entry hyperlink to a search result for that particular person
             //requires toc.php to change to GET instead of POST, and be able to pull the parameters out of the URL
             //TODO: get the names from $row here to properly escape

             $ncount = 0;

             echo  '<div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">';

              echo  '<div class="panel panel-default" style="width:300px">';
                echo  '<div class="panel-heading" role="tab" id="ViewA-M" data-toggle="collapse" data-parent="#accordion" data-target="#collapseViewA-M">';
                  echo '<h2 class="panel-title" style="text-align:center">';
                    echo '<a class="accordion-toggle">';
                      echo 'A-M';
                    echo '</a>';
                  echo '</h2>';
                echo '</div>';

              echo '<div id="collapseViewA-M" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ViewA-M">';
                echo '<div class="panel-body">';

             foreach($dbh->query('SELECT lastname,firstname, COUNT(*) AS "numOfLetters" from letters GROUP BY lastname, firstname;') as $row) {
                 //var_dump($row);
                 //print_r($row); echo '<br/><br/>';

                 if(strtolower(substr($row['lastname'],0,1)) > "m") { //the letter would be "n, o, p, q, r, s, t..."
                    $ncount++;
                 } 

                 if($ncount === 1) {

                  echo '</div>';
                  echo '</div>';
                  echo '</div>'; //close out the A-M accordion divs

                  //start the N-Z accordion div
                  echo  '<div class="panel panel-default" style="width:300px">';
                    echo  '<div class="panel-heading" role="tab" id="ViewN-Z" data-toggle="collapse" data-parent="#accordion" data-target="#collapseViewN-Z">';
                      echo '<h2 class="panel-title" style="text-align:center">';
                        echo '<a class="accordion-toggle">';
                          echo 'N-Z';
                        echo '</a>';
                      echo '</h2>';
                    echo '</div>';

                  echo '<div id="collapseViewN-Z" class="panel-collapse collapse" role="tabpanel" aria-labelledby="ViewN-Z">';
                    echo '<div class="panel-body">';

                 }

                 //hard-to-read echo, can break the URL into a query string variable if desired
                 echo '<a href="viewauthor.php?firstname=' . urlencode($row['firstname']) . '&lastname=' . urlencode($row['lastname']) . '"><p class="resultRow"><span class="lastname">' . $row['lastname'] . ',' . ' </span><span class="firstname">' . $row['firstname'] . ' (</span><span class="numOfLetters">' . $row['numOfLetters'] . '</span> letters)</p></a>';
             }

             if ($ncount > 0) { //if we ever even made the n-z div

                  echo '</div>';
                  echo '</div>';
                  echo '</div>'; //close out the N-Z accordion divs
             }

             //in either case, close out the panel-group div
             echo '</div>';

             $dbh = null; //connection closed
         } catch (PDOException $e) {
             print "Error!: " . $e->getMessage() . "<br/>";
             die();
         }

      ?>

      <!-- <p>TODO: put letter info dump / variables of interest from MySQL here</p> -->
   </body>

</html>
