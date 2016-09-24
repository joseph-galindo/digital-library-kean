function legend(map) {

	var legend = document.getElementById('legend');
	legend.style.display = "block"; //toggle visibility here, when it's known the legend will actually be needed 
	
	var prefixes = ["unknown","1941","1942","1943","1944","1945","1946","1950"];

	for(var i = 0; i < prefixes.length; i++) {

		var single_icon = "marker_icons/" + prefixes[i] + "_single.png";
		var multiple_icon = "marker_icons/" + prefixes[i] + "_multiple.png";

		var single_name = prefixes[i] + " (single)";
		var multiple_name = prefixes[i] + " (multiple)";

		var div = document.createElement('div');

		div.innerHTML = '<img src="' + single_icon + '"> ' + single_name + '<img src="' + multiple_icon + '"> ' + multiple_name;
		legend.appendChild(div);
	}

	map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend); //push the filled legend div to the map

}

function toggle_legend_visibility() {

	var legend = document.getElementById('legend');

	if(legend.style.display == "block") { //if visible, hide it

		legend.style.display = "none";
	}

	else { //if hidden, show it

		legend.style.display = "block";
	}
}