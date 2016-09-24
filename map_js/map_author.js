    var map; //global ref to google map
    var center; //global ref to the center of a map with multiple letters
    var valid_markers = []; //only the markers with real location data
    var current_zoomed_coord_index = -1; //iterator for stepping through letters in chrono order -- initially at -1, then set to [0,+infinity]
    var infoWindow = new google.maps.InfoWindow({maxWidth: 200}); //global infowindow for letter info

    function load(firstname, lastname) {

      var string1 = "getxml.php?";
      var string2 = "firstname=";
      string2 = string2.concat(firstname);
      var string3 = "&lastname=";
      string3 = string3.concat(lastname);
      var phplink = string1.concat(string2,string3);

      var markers = []; //all markers, including potentially null location data ones

      downloadUrl(phplink, function(data) {

        var xml = data.responseXML;
        markers = xml.documentElement.getElementsByTagName("marker");

        if(markers.length === 1) { //***author only had 1 letter***

          if(markers[0].getAttribute("lat") && markers[0].getAttribute("lng"))  {

            map = new google.maps.Map(document.getElementById("map"), {
            center: new google.maps.LatLng(markers[0].getAttribute("lat"), markers[0].getAttribute("lng")),
            zoom: 2,
            mapTypeId: 'roadmap'
            }); //create map, center is one pt

            document.getElementById("maperror").style.display = "none";

            center = map.center;
          }

          else {

            document.getElementById("maperror").innerHTML = "Location data is not available at this time.<br>";
          
            document.getElementById("map").style.display = "none";
            document.getElementById("marker_traveller").style.display = "none";
            return; //exit early so that the legend div is never created
          }

        }

        else { //***author had > 1 letter***

          var bound = new google.maps.LatLngBounds();
          var nullcount = 0;

          for(i = 0; i < markers.length; i++) {

            if(markers[i].getAttribute("lat") && markers[i].getAttribute("lng")) { //if the point is NOT NULL

              bound.extend(new google.maps.LatLng(markers[i].getAttribute("lat"), markers[i].getAttribute("lng")));
            }

            else { //both the lat and lng are null, meaning we don't have a point to map it to

              nullcount++; //keep track of how many null points there are
            }
          }

          if(nullcount !== markers.length) { //if there is at least one non-null point, make the map object

            map = new google.maps.Map(document.getElementById("map"), {
            center: bound.getCenter(),
            zoom: 2,
            mapTypeId: 'roadmap'
            }); //create map, center is derived from latlngbounds

            center = bound.getCenter(); //store center in a global

            document.getElementById("maperror").style.display = "none";
          }

          else { //nullcount === markers.length, which means every point for the author was null, so DO NOT draw the map

            document.getElementById("maperror").innerHTML = "Location data is not available at this time.<br>";
          
            document.getElementById("map").style.display = "none";
            document.getElementById("marker_traveller").style.display = "none";
            return; //exit early so that the legend div is never created
          }
        }

        var oms = new OverlappingMarkerSpiderfier(map,{
          keepSpiderfied: true
        });

          var spider_infowindow = new google.maps.InfoWindow({maxWidth: 200});

          /* Not needed -- let the google maps API infowindow control clicks instead for now
          oms.addListener('click', function(marker, event) {
            iw.setContent(marker.desc);
            iw.open(map, marker);
            }); */

          oms.addListener('spiderfy',function(markers) {
            spider_infowindow.close();
          })

          var distinct_array = {};

          for(i = 0; i < markers.length; i++) {
            //get distinct marker lat/lng combos
            if(markers[i].getAttribute("lat") && markers[i].getAttribute("lng")) {

              var coords = markers[i].getAttribute("lat")+","+markers[i].getAttribute("lng");
              if(distinct_array[coords])
                distinct_array[coords]++;
              else
                distinct_array[coords] = 1;
            }
          }

        var lineSymbol = {
          path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW
        };

        var chronocounter = 0;

        for(var i = 0; i < markers.length; i++) {

          if(markers[i].getAttribute("lat") && markers[i].getAttribute("lng")) { //if the map point exists

            chronocounter++;

            var coords = markers[i].getAttribute("lat")+","+markers[i].getAttribute("lng");

            var name = markers[i].getAttribute("name");
            var address = markers[i].getAttribute("address");
            var point = new google.maps.LatLng(
              parseFloat(markers[i].getAttribute("lat")),
              parseFloat(markers[i].getAttribute("lng")));
            var letterid = markers[i].getAttribute("letterid");
            var year = markers[i].getAttribute("year");

            if(year > "0000") { //if a year is not null, by default make the path point to that year, single icon on the spot

              var marker_icon_filepath = "marker_icons/" + year.toString() + "_single.png";
            }

            else { //year is unknown

              var marker_icon_filepath = "marker_icons/unknown_single.png";
            }

            var html = "<b><a href=viewletter.php?letterid=" + letterid + ">" + 
                        name + "</a>" + 
                        "</b> <br>" + 
                        address +
                        "<br>";
            var icon = {};

            //change this to generate string that points to proper marker, then use that in the icon -> we can go from this to use normal markers instead of MarkerWithLabel
            //year maps to color - add year to getxml so that we can easily pull it like the data above
            //order maps to marker content - use chronocounter in the string generation
            //remove the above var icon, and directly point to the generated string instead
            //problem: this approach doesn't map density -- may need colored *and* shaped marker

            //when this works, build a legend for the map

            var marker = new MarkerWithLabel({
              map: map,
              position: point,
              icon: marker_icon_filepath,
              name: name,
              labelAnchor: new google.maps.Point(3,27),
              year: year,
              chronoSpot: chronocounter //label markers with the order they appear (chronologically)
            });

            if(chronocounter >= 10) { //slightly move the anchor to make 2-digit counters appear well

              marker.setOptions({
                labelAnchor: new google.maps.Point(6,27)
              });
            }

          //if the points are going to be spiderfied, add them to the oms instance
          //this conditional weeds out points that fall on the same spot

          if(distinct_array[coords] > 1) {
            oms.addMarker(marker);
            
            if(year > "0000") {
              marker.setOptions({
                icon: "marker_icons/" + year.toString() + "_multiple.png"
              }); 
            }

            else {
              marker.setOptions({
                icon: "marker_icons/unknown_multiple.png"
              });
            } 
          }

          valid_markers.push(marker);
          
          bindInfoWindow(marker, map, infoWindow, html); //bind ALL points to the google.maps.event

          } //endif
        } //end marker loop

      for(var i = 0; i < valid_markers.length-1; i++) {

        if(!valid_markers[i].position.equals(valid_markers[i+1].position) && valid_markers[i].year > "0000" && valid_markers[i+1].year > "0000") { //if the pair is on the exact same spot, OR one of the two points has an unknown date, do not draw the polyline

          var midpoint = google.maps.geometry.spherical.interpolate(valid_markers[i].position,valid_markers[i+1].position,0.5); //midway pt between these two markers

          var polyLabel = new Label({
            text: valid_markers[i].name + " to " + valid_markers[i+1].name,
            position: midpoint
          });

          var path = new google.maps.Polyline({
            path: [valid_markers[i].position,valid_markers[i+1].position],
            icons: [{
              icon: lineSymbol,
              offset: '100%'
            }],
            geodesic: true,
            strokeColor: '#2f2f2f',
            strokeOpacity: 1.0,
            strokeWeight: 4,
            map: map
          });

          create_polyline_listeners(path, polyLabel);
        } //endif
      } //endloop

        legend(map); //call the legend function in create_legend.js

        document.getElementById("traversal").innerHTML = "Use the buttons below to step through the letters chronologically.";

      }); //end downloadUrl call
    } //end the load function

    function create_polyline_listeners(polyline, label) {

      google.maps.event.addListener(polyline, 'mouseover', function(e) {

          var tooltipLat = e.latLng.lat();
          var tooltipLng = e.latLng.lng();
          label.position = new google.maps.LatLng(tooltipLat, tooltipLng);
          //label.position = e.latLng;

          label.setMap(map);
          
          polyline.setOptions({ strokeColor: '#660033',
                                strokeWeight: 6});  
      });

      google.maps.event.addListener(polyline, 'mouseout', function(e) {

          label.setMap(null);
          polyline.setOptions({ strokeColor: '#2f2f2f',
                                strokeWeight: 4});  
      });
    }

    function goto_first_marker() {

      map.setZoom(7);
      current_zoomed_coord_index = 0; //reset current zoomed coord index
      map.panTo(valid_markers[current_zoomed_coord_index].position);
      google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
      document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
    }

    function goto_final_marker() {

      map.setZoom(7);
      current_zoomed_coord_index = valid_markers.length-1; //reassign current zoomed coord
      map.panTo(valid_markers[current_zoomed_coord_index].position);
      google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
      document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
    }

    function goto_previous_marker() {

      if(current_zoomed_coord_index === 0 || current_zoomed_coord_index === -1) {

        //if the user navigated to the start of the path, OR has freshly loaded the map and not used the buttons yet, OR reset the map view, wrap around to the end
        map.setZoom(7);
        current_zoomed_coord_index = valid_markers.length-1 //reassign current zoomed coord
        map.panTo(valid_markers[current_zoomed_coord_index].position);
        google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
        document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
      }

      else {

        //the user is somewhere from coords[1] to coords[coords.length-1], so go to previous marker as expected
        map.setZoom(7);
        current_zoomed_coord_index--;
        map.panTo(valid_markers[current_zoomed_coord_index].position);
        google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
        document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";     
      }
    }

    function goto_next_marker() {

      if(current_zoomed_coord_index === valid_markers.length-1) {

        //if the user navigated to the end of the path, wrap around to the beginning
        map.setZoom(7);
        current_zoomed_coord_index = 0; //reassign current zoomed coord
        map.panTo(valid_markers[current_zoomed_coord_index].position);
        google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
        document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
      }

      else {

        //the user is somwhere from coords[-1] to coords[coords.length-2], go to next as expected
        map.setZoom(7);
        current_zoomed_coord_index++;
        map.panTo(valid_markers[current_zoomed_coord_index].position);
        google.maps.event.trigger(valid_markers[current_zoomed_coord_index],'click');
        document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
      }
    }

    function reset_map_view() {

      map.setZoom(2);
      map.setCenter(center);
      infoWindow.close();
      document.getElementById("traversal").innerHTML = "Use the buttons below to step through the letters chronologically."; //reset the traversal text
      current_zoomed_coord_index = -1; //reset current zoomed coord index as if the map was freshly loaded
    }

    function bindInfoWindow(marker, map, infoWindow, html) {

      google.maps.event.addListener(marker, 'click', function() {

        infoWindow.setContent(html);
        infoWindow.open(map,marker);

        //extra code to bind the map navigation to marker clicks
        current_zoomed_coord_index = marker.chronoSpot-1; //-1 for 0-indexing
        document.getElementById("traversal").innerHTML = "Letter " + (current_zoomed_coord_index+1) + " of " + valid_markers.length + " (chronologically)";
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

      var flag = 0;

      //someone opened the map accordion
      
      $("#collapseViewMap").on('show.bs.collapse', function() {

        if(flag === 0) {

          setTimeout(function() { //wait shortly so the resize is done properly, only resize once on the first map opening

            google.maps.event.trigger(map,'resize');
            map.setCenter(center);
          }, 1000);

          flag++;
        }

      }); //end collapsemap listener

      var imageCarousel = $(".carousel");
      var indicators = $(".carousel-indicators");

      imageCarousel.find(".carousel-inner").children(".item").each(function(index) {

        if(index === 0)
          indicators.append('<li data-target="#carousel" data-slide-to="'+index+'" class="active"></li>');
        else
          indicators.append('<li data-target="#carousel" data-slide-to="'+index+'"></li>');
      });

      $("#collapseViewImages").on('show.bs.collapse', function() {

     	  $(".carousel").carousel({
	       interval:2500
        });

      }); //end collapseimage listener

    }); //end document ready listener
