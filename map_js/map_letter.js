    var map;
    var center;
    var marker;
    var infoWindow;

    function load(letterid) {

      infoWindow = new google.maps.InfoWindow({maxWidth: 200});

      var string1 = "getxml.php?";
      var string2 = "letterid=";
      string2 = string2.concat(letterid);
      var phplink = string1.concat(string2);

      downloadUrl(phplink, function(data) {

        var xml = data.responseXML;
        var markers = xml.documentElement.getElementsByTagName("marker");

        //try to change map center based on marker points
        if(markers.length === 1){

          if(markers[0].getAttribute("lat") && markers[0].getAttribute("lng")) { //if the point has a non-null lat, lng

            map = new google.maps.Map(document.getElementById("map"), {
            center: new google.maps.LatLng(markers[0].getAttribute("lat"), markers[0].getAttribute("lng")),
            zoom: 5,
            mapTypeId: 'roadmap'
            }); //create map, center is one pt

            center = map.center; //store in global

            document.getElementById("maperror").style.display = "none";
          }

          else { //the point has a null lat and a null lng

            document.getElementById("maperror").innerHTML = "Location data is not available at this time.<br>";
        
            document.getElementById("map").style.display = "none";
            document.getElementById("button_container").style.display = "none";
            return; //exit early so that the legend div is never created
          }
        }

        else { //length is !==1; length for letters must be either 1 (real data or null data) or 0 (no row entry yet)

            document.getElementById("maperror").innerHTML = "Location data is not available at this time.<br>";
        
            document.getElementById("map").style.display = "none";
            document.getElementById("button_container").style.display = "none";
            return; //exit early so that the legend div is never created
        }

        for(var i = 0; i < markers.length; i++) {

          var name = markers[i].getAttribute("name");
          var address = markers[i].getAttribute("address");
          var point = new google.maps.LatLng(
            parseFloat(markers[i].getAttribute("lat")),
            parseFloat(markers[i].getAttribute("lng")));

          var year = markers[i].getAttribute("year");

          if(year > "0000") { //if a year is not null, by default make the path point to that year, single icon on the spot

            var marker_icon_filepath = "marker_icons/" + year.toString() + "_single.png";
          }

          else { //year is unknown

            var marker_icon_filepath = "marker_icons/unknown_single.png";
          }

          var html = "<b>" + name + "</b> <br/>" + address;
          var icon = {};
          marker = new google.maps.Marker({
            map: map,
            position: point,
            icon: marker_icon_filepath
          });

          bindInfoWindow(marker, map, infoWindow, html);
        }//end marker loop

        legend(map); //call the legend function in create_legend.js
        
      }); //end downloadUrl call
    } //end the load function

    function reset_map_view() {

      map.setZoom(5);
      map.setCenter(marker.position);
      infoWindow.close();
    }

    function bindInfoWindow(marker, map, infoWindow, html) {

      google.maps.event.addListener(marker, 'click', function() {

        infoWindow.setContent(html);
        infoWindow.open(map,marker);
      });
    }

    function downloadUrl(url, callback) {

      var request = window.ActiveXObject ?
        new ActiveXObject('Microsoft.XMLHTTP') :
        new XMLHttpRequest;

      request.onreadystatechange = function() {

        if(request.readyState === 4) {

          request.onreadystatechange = doNothing;
          callback(request, request.status);
        }
      };

      request.open('GET', url, true);
      request.send(null);
    }

    function doNothing() {}

    //do some jquery event listening
    $(document).ready(function() { //when the document is loaded and ready, listen for stuff

      //someone opened the map accordion
      $('#collapseViewMap').on('show.bs.collapse', function() {

        setTimeout(function() { //wait shortly so the resize is done properly

          google.maps.event.trigger(map,'resize');
          map.setCenter(center);
        }, 1000);
      }); //end mapaccordion listener

      var imageCarousel = $(".carousel");
      var indicators = $(".carousel-indicators");

      imageCarousel.find(".carousel-inner").children(".item").each(function(index) {

        if(index === 0)
          indicators.append('<li data-target="#carousel" data-slide-to="'+index+'" class="active"></li>');
        else
          indicators.append('<li data-target="#carousel" data-slide-to="'+index+'"></li>');
      });

      $('#collapseViewImages').on('show.bs.collapse', function() {

        $(".carousel").carousel({
         interval:2500
        });

      }); //end collapseimage listener

    });