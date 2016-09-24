<?php 

    function setQueryVars(&$year) {

      $year = -1;

      if(isset($_GET['year']) && !empty($_GET['year'])) {
        $year = $_GET['year'];
      }

      else {
        include 'menu.php';
        echo '<p>You seem to be missing some important information. Please <a href="index.php">browse the index</a> to find a year you would like to view.</p>';

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
      #legend { background:white; padding:10px; display:none;}
      .panel-heading {cursor:pointer;}
    </style>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">

    <?php setQueryVars($year); ?>

    <meta charset="UTF-8">
    <title>

    <?php

    echo 'Year: ' . $year;

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
   <script type="text/javascript" src="map_js/map_year.js"></script>
   <script type="text/javascript" src="map_js/create_legend.js"></script>

</head>

  <body onload="load(&#34;<?php echo $year; ?>&#34;)">

        <?php //output goes here! 

        include 'menu.php';

        echo '<h1>View Year: ' . $year . '</h1>'; ?>

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

  </body>
</html>