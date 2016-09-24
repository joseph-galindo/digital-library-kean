<html>

 <head>

    <meta charset="UTF-8">
    <title>Nancy Thompson Library: Years Page</title>

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

   <!-- small script for dropdown redirection -->
   <script type="text/javascript">

   //listen when the document is ready
   $(document).ready(function() {

      $("#yearDropdown").on('change', function() {

          if($("#yearDropdown option:selected").index() !== 0) {

            window.location.href = $("#yearDropdown").val();
          }
      }); //end select listener

    }); //end doc ready listener

   </script>

 </head>

 <body>

  <?php include 'menu.php'; ?>

  <h1>Years</h1>
 	<h3>Please select a year from the options below.</h3>
  (You will be redirected upon making a choice). <br><br>

<!-- old list
 	<ul>
 		<li><a href="viewyear.php?year=1940">1940</a></li>
 		<li><a href="viewyear.php?year=1941">1941</a></li>
 		<li><a href="viewyear.php?year=1942">1942</a></li>
 		<li><a href="viewyear.php?year=1943">1943</a></li>
 		<li><a href="viewyear.php?year=1944">1944</a></li>
 		<li><a href="viewyear.php?year=1945">1945</a></li>
 		<li><a href="viewyear.php?year=all">All</a></li>
 	</ul>
-->

  <select name="yearDropdown" id="yearDropdown">
    <option value="">----</option>
    <option value="viewyear.php?year=All">All</option>
    <option value="viewyear.php?year=1941">1941</option>
    <option value="viewyear.php?year=1942">1942</option>
    <option value="viewyear.php?year=1943">1943</option>
    <option value="viewyear.php?year=1944">1944</option>
    <option value="viewyear.php?year=1945">1945</option>
    <option value="viewyear.php?year=1946">1946</option>
    <option value="viewyear.php?year=1950">1950</option>
  </select>

 </body>

</html>
