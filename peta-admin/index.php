<?php
include '../login/middleware.php';
checkAuthentication(); // Call the middleware function to check authentication
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="initial-scale=1,user-scalable=no,maximum-scale=1,width=device-width" />
  <meta name="mobile-web-app-capable" content="yes" />
  <meta name="apple-mobile-web-app-capable" content="yes" />
  <link rel="stylesheet" href="css/leaflet.css" />
  <link rel="stylesheet" href="css/L.Control.Locate.min.css" />
  <link rel="stylesheet" href="css/qgis2web.css" />
  <link rel="stylesheet" href="css/fontawesome-all.min.css" />
  <link rel="stylesheet" href="css/leaflet-search.css" />
  <link rel="stylesheet" href="css/filter.css" />
  <link rel="stylesheet" href="css/nouislider.min.css" />
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.3.2/html2canvas.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <style>
    html,
    body,
    #map {
      width: 100%;
      height: 100%;
      padding: 0;
      margin: 0;
      overflow: hidden;
      overflow-x: hidden;
    }

    #runCmd {
      position: fixed;
      top: 455px;
      right: 90px;
      z-index: 1000;
      padding: 10px 20px;
      background-color: #3498db;
      color: #ffffff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
    }

    #runCmd:hover {
      background-color: #2980b9;
    }

    /* The Modal (background) */
    .modal {
      display: none;
      overflow-y: hidden !important;
      /* Hidden by default */
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgb(0, 0, 0);
      background-color: rgba(0, 0, 0, 0.4);
    }

    /* Modal Content */
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 20%;
      text-align: center;
      margin-top: 150px;
    }

    /* The Close Button */
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    /*Legend specific*/
    .legend {
      padding: 6px 8px;
      font: 14px Arial, Helvetica, sans-serif;
      background: white;
      background: rgba(255, 255, 255, 0.8);
      /*box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);*/
      /*border-radius: 5px;*/
      line-height: 24px;
      color: #555;
    }

    .legend h4 {
      text-align: center;
      font-size: 16px;
      margin: 2px 12px 8px;
      color: #777;
    }

    .legend span {
      position: relative;
      bottom: 3px;
    }

    .legend i {
      width: 18px;
      height: 18px;
      float: left;
      margin: 0 8px 0 0;
      opacity: 0.7;
    }

    .legend p {
      margin-bottom: 0px;
      display: inline-block;
    }

    .legend i.icon {
      background-size: 18px;
      background-color: rgba(255, 255, 255, 1);
    }

    .dot {
      background-color: #bbb;
      border-radius: 50%;
      display: inline-block;
      margin-left: 8px;
    }

    .square {
      width: 18px;
      height: 18px;
      margin: 0 5px 0 5px;
      opacity: 0.7;
      display: inline-block;
    }

    .compass-control img {
      width: 150px;
      /* Adjust the width as needed */
      height: auto;
      /* This will keep the aspect ratio of the image */
    }
  </style>
  <title>PETA ADMIN</title>
</head>

<body>
  <button id="runCmd">Update Data</button>
  <!-- The Modal -->
  <div id="myModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <pre id="output"></pre>
      <button id="refreshPage">OK</button>
    </div>
  </div>

  <div id="map"></div>
  <script src="js/qgis2web_expressions.js"></script>
  <script src="js/leaflet.js"></script>
  <script src="js/L.Control.Locate.min.js"></script>
  <script src="js/leaflet.rotatedMarker.js"></script>
  <script src="js/leaflet.pattern.js"></script>
  <script src="js/leaflet-image.js"></script>
  <script src="js/leaflet-hash.js"></script>
  <script src="js/Autolinker.min.js"></script>
  <script src="js/rbush.min.js"></script>
  <script src="js/labelgun.min.js"></script>
  <script src="js/labels.js"></script>
  <script src="js/leaflet-search.js"></script>
  <script src="js/tailDT.js"></script>
  <script src="js/nouislider.min.js"></script>
  <script src="js/wNumb.js"></script>
  <script src="data/Pointsfromtable_1.js"></script>
  <script src="js/node_modules/leaflet-easyprint/dist/bundle.js"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      var modal = document.getElementById('myModal');
      var span = document.getElementsByClassName("close")[0];

      $("#runCmd").click(function() {
        $.ajax({
          url: 'update.php', // URL pointing to your geojson.php
          method: 'GET', // Assuming geojson.php is to be accessed via GET
          success: function(response) {
            $("#output").html(response); // Update the modal content with the response
            modal.style.display = "block"; // Show the modal
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.log("Error: ", textStatus, errorThrown);
            $("#output").html("Error fetching data."); // Display an error message in the modal
            modal.style.display = "block"; // Still show the modal to inform the user
          }
        });
      });

      // When the user clicks on <span> (x), close the modal
      span.onclick = function() {
        modal.style.display = "none";
      }

      // When the user clicks the "OK" button, close the modal and refresh the page
      $("#refreshPage").click(function() {
        location.reload(true);
      });

      // When the user clicks anywhere outside of the modal, close it
      window.onclick = function(event) {
        if (event.target == modal) {
          modal.style.display = "none";
        }
      }

    });
  </script>

  <script>
    var highlightLayer;

    function highlightFeature(e) {
      highlightLayer = e.target;

      if (e.target.feature.geometry.type === "LineString") {
        highlightLayer.setStyle({
          color: "#FFFFFF",
        });
      } else {
        highlightLayer.setStyle({
          fillColor: "#FFFFFF",
          fillOpacity: 1,
        });
      }
    }
    var map = L.map("map", {
      zoomControl: true,
      maxZoom: 28,
      minZoom: 1,
    }).fitBounds([
      [-6.32630369961541, 87.49134402946176],
      [8.36968348656025, 106.76915188489359],
    ]);

    var hash = new L.Hash(map);
    map.attributionControl.setPrefix(
      '<a href="https://github.com/tomchadwin/qgis2web" target="_blank">qgis2web</a> &middot; <a href="https://leafletjs.com" title="A JS library for interactive maps">Leaflet</a> &middot; <a href="https://qgis.org">QGIS</a>'
    );
    var autolinker = new Autolinker({
      truncate: {
        length: 30,
        location: "smart"
      },
    });
    L.control.locate({
      locateOptions: {
        maxZoom: 19
      }
    }).addTo(map);

    var bounds_group = new L.featureGroup([]);

    function setBounds() {}
    map.createPane("pane_OSMStandard_0");
    map.getPane("pane_OSMStandard_0").style.zIndex = 400;
    var layer_OSMStandard_0 = L.tileLayer(
      "http://tile.openstreetmap.org/{z}/{x}/{y}.png", {
        pane: "pane_OSMStandard_0",
        opacity: 1.0,
        attribution: '<a href="https://www.openstreetmap.org/copyright">© OpenStreetMap contributors, CC-BY-SA</a>',
        minZoom: 1,
        maxZoom: 28,
        minNativeZoom: 0,
        maxNativeZoom: 19,
      }
    );
    layer_OSMStandard_0;
    map.addLayer(layer_OSMStandard_0);


    function pop_Pointsfromtable_1(feature, layer) {
      layer.on({
        mouseout: function(e) {
          for (i in e.target._eventParents) {
            e.target._eventParents[i].resetStyle(e.target);
          }
        },
        mouseover: highlightFeature,
      });
      var popupContent =
        '<table>\
                    <tr>\
                        <th scope="row">Waktu</th>\
                        <td>' +
        (feature.properties["ot_wib"] !== null ?
          autolinker.link(feature.properties["ot_wib"].toLocaleString()) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Longitude</th>\
                        <td>' +
        (feature.properties["lon"] !== null ?
          autolinker.link(feature.properties["lon"].toLocaleString()) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Latitude</th>\
                        <td>' +
        (feature.properties["lat"] !== null ?
          autolinker.link(feature.properties["lat"].toLocaleString()) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Magnitude</th>\
                        <td>' +
        (feature.properties["mag"] !== null ?
          autolinker.link(feature.properties["mag"].toLocaleString()) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Depth (km)</th>\
                        <td>' +
        (feature.properties["dept"] !== null ?
          autolinker.link(feature.properties["dept"].toLocaleString()) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Kota Terdekat</th>\
                        <td>' +
        (feature.properties["kota_terdekat"] !== null ?
          autolinker.link(
            feature.properties["kota_terdekat"].toLocaleString()
          ) :
          "") +
        '</td>\
                    </tr>\
                    <tr>\
                        <th scope="row">Keterangan</th>\
                        <td>' +
        (feature.properties["ket"] !== null ?
          autolinker.link(feature.properties["ket"].toLocaleString()) :
          "") +
        "</td>\
                    </tr>\
                </table>";
      layer.bindPopup(popupContent, {
        maxHeight: 400
      });
    }


    function style_Pointsfromtable_1_0(feature) {
      var magnitude = Number(feature.properties.mag);
      var depth = Number(feature.properties.dept);
      var radius;
      var color;

      // Adjust radius based on magnitude and zoom level
      var zoomLevel = map.getZoom();
      var baseRadius = 10; // Base radius, adjust as needed
      if (magnitude <= 3) {
        radius = baseRadius;
      } else if (magnitude >= 3.1 && magnitude <= 5) {
        radius = baseRadius * 2;
      } else if (magnitude >= 5.1) {
        radius = baseRadius * 3.5;
      }

      radius *= zoomLevel / 13; // Scale radius based on zoom level

      // Set color based on depth
      if (depth <= 60) {
        color = 'red';
      } else if (depth <= 300) {
        color = 'yellow';
      } else if (depth > 301) {
        color = 'green';
      } else {
        color = 'black'; // For unknown depth
      }

      return {
        pane: "pane_Pointsfromtable_1",
        radius: radius,
        opacity: 1,
        color: "rgba(35,35,35,1.0)",
        dashArray: "",
        lineCap: "butt",
        lineJoin: "miter",
        weight: 1,
        fill: true,
        fillOpacity: 1,
        fillColor: color,
        interactive: true,
      };
    }



    map.createPane("pane_Pointsfromtable_1");
    map.getPane("pane_Pointsfromtable_1").style.zIndex = 401;
    map.getPane("pane_Pointsfromtable_1").style["mix-blend-mode"] = "normal";
    var layer_Pointsfromtable_1 = new L.geoJson(json_Pointsfromtable_1, {
      attribution: "",
      interactive: true,
      dataVar: "json_Pointsfromtable_1",
      layerName: "layer_Pointsfromtable_1",
      pane: "pane_Pointsfromtable_1",
      onEachFeature: pop_Pointsfromtable_1,
      pointToLayer: function(feature, latlng) {
        var context = {
          feature: feature,
          variables: {},
        };
        return L.circleMarker(latlng, style_Pointsfromtable_1_0(feature));
      },
    });
    bounds_group.addLayer(layer_Pointsfromtable_1);
    map.addLayer(layer_Pointsfromtable_1);
    setBounds();
    map.addControl(
      new L.Control.Search({
        layer: layer_Pointsfromtable_1,
        initial: false,
        hideMarkerOnCollapse: true,
        propertyName: "ket",
      })
    );
    L.easyPrint({
      title: 'Download Map',
      position: 'topleft',
      sizeModes: ['A4Landscape', 'A4Portrait'],
      filename: 'Peta Gempa',
      exportOnly: true,
      hideControlContainer: false,
    }).addTo(map);

    /*Legend specific*/
    var legend = L.control({
      position: "bottomleft"
    });

    legend.onAdd = function(map) {
      var div = L.DomUtil.create("div", "legend");
      div.innerHTML += "<h4>LEGEND</h4>";
      div.innerHTML += '<hr style="border: 2px solid white; margin:1px;"><small>Magnitude :</small><br><span class="dot" style="height: 10px; width: 10px;"></span>';
      div.innerHTML += '<p><=3 </p><span class="dot" style="height: 20px; width: 20px;"></span>';
      div.innerHTML += '<p><=5 </p><span class="dot" style="height: 35px; width: 35px;"></span>';
      div.innerHTML += '<p>>5 </p>';
      // div.innerHTML += '<p><=4.99 </p><span class="dot" style="height: 17px; width: 17px;"></span>';
      // div.innerHTML += '<p><=5.99 </p><span class="dot" style="height: 20px; width: 20px;"></span>';
      // div.innerHTML += '<p><=6.99 </p><span class="dot" style="height: 23px; width: 23px;"></span>';
      // div.innerHTML += '<p><=7.99 </p><span class="dot" style="height: 26px; width: 26px;"></span>';
      div.innerHTML += '<small><br>Depth (Km) :</small><br>';
      div.innerHTML += '<span class="square" style="background: red"></span><small>0-60</small>';
      div.innerHTML += '<span class="square" style="background: yellow"></span><small>61-300</small>';
      div.innerHTML += '<span class="square" style="background: green"></span><small>300+</small>';
      // div.innerHTML += '<span class="square" style="background: greenyellow"></span><small>&lt;=600</small>';
      // div.innerHTML += '<span class="square" style="background: green"></span><small>&gt;600</small>';

      return div;
    };

    legend.addTo(map);

    var compass = L.control({
      position: 'topright'
    });

    compass.onAdd = function(map) {
      var div = L.DomUtil.create('div', 'compass-control');
      div.innerHTML = '<img src="images/compass.png" alt="Compass Rose">';
      return div;
    };

    compass.addTo(map);



    document.getElementsByClassName("search-button")[0].className +=
      " fa fa-binoculars";
    var mapDiv = document.getElementById("map");
    var row = document.createElement("div");
    row.className = "row";
    row.id = "all";
    row.style.height = "100%";
    var col1 = document.createElement("div");
    col1.className = "col9";
    col1.id = "mapWindow";
    col1.style.height = "99%";
    col1.style.width = "80%";
    col1.style.display = "inline-block";
    var col2 = document.createElement("div");
    col2.className = "col3";
    col2.id = "menu";
    col2.style.display = "inline-block";
    mapDiv.parentNode.insertBefore(row, mapDiv);
    document.getElementById("all").appendChild(col1);
    document.getElementById("all").appendChild(col2);
    col1.appendChild(mapDiv);
    var Filters = {
      ot_wib: "datetime",
      dept: "int",
      mag: "real",
      lon: "real",
      lat: "real",
    };

    function filterFunc() {
      map.eachLayer(function(lyr) {
        if ("options" in lyr && "dataVar" in lyr["options"]) {
          features = this[lyr["options"]["dataVar"]].features.slice(0);
          try {
            for (key in Filters) {
              keyS = key.replace(/[^a-zA-Z0-9_]/g, "");
              if (Filters[key] == "str" || Filters[key] == "bool") {
                var selection = [];
                var options = document.getElementById("sel_" + keyS).options;
                for (var i = 0; i < options.length; i++) {
                  if (options[i].selected) selection.push(options[i].value);
                }
                try {
                  if (key in features[0].properties) {
                    for (i = features.length - 1; i >= 0; --i) {
                      if (
                        selection.indexOf(features[i].properties[key]) < 0 &&
                        selection.length > 0
                      ) {
                        features.splice(i, 1);
                      }
                    }
                  }
                } catch (err) {}
              }
              if (Filters[key] == "int") {
                sliderVals = document
                  .getElementById("div_" + keyS)
                  .noUiSlider.get();
                try {
                  if (key in features[0].properties) {
                    for (i = features.length - 1; i >= 0; --i) {
                      if (
                        parseInt(features[i].properties[key]) <
                        sliderVals[0] ||
                        parseInt(features[i].properties[key]) > sliderVals[1]
                      ) {
                        features.splice(i, 1);
                      }
                    }
                  }
                } catch (err) {}
              }
              if (Filters[key] == "real") {
                sliderVals = document
                  .getElementById("div_" + keyS)
                  .noUiSlider.get();
                try {
                  if (key in features[0].properties) {
                    for (i = features.length - 1; i >= 0; --i) {
                      if (
                        features[i].properties[key] < sliderVals[0] ||
                        features[i].properties[key] > sliderVals[1]
                      ) {
                        features.splice(i, 1);
                      }
                    }
                  }
                } catch (err) {}
              }
              if (
                Filters[key] == "date" ||
                Filters[key] == "datetime" ||
                Filters[key] == "time"
              ) {
                try {
                  if (key in features[0].properties) {
                    HTMLkey = key.replace(/[&\/\\#,+()$~%.'":*?<>{} ]/g, "");
                    startdate = document
                      .getElementById("dat_" + HTMLkey + "_date1")
                      .value.replace(" ", "T");
                    enddate = document
                      .getElementById("dat_" + HTMLkey + "_date2")
                      .value.replace(" ", "T");
                    for (i = features.length - 1; i >= 0; --i) {
                      if (
                        features[i].properties[key] < startdate ||
                        features[i].properties[key] > enddate
                      ) {
                        features.splice(i, 1);
                      }
                    }
                  }
                } catch (err) {}
              }
            }
          } catch (err) {}
          this[lyr["options"]["layerName"]].clearLayers();
          this[lyr["options"]["layerName"]].addData(features);
        }
      });
    }
    document
      .getElementById("menu")
      .appendChild(document.createElement("div"));
    var div_ot_wib_date1 = document.createElement("div");
    div_ot_wib_date1.id = "div_ot_wib_date1";
    div_ot_wib_date1.className = "filterselect";
    document.getElementById("menu").appendChild(div_ot_wib_date1);
    dat_ot_wib_date1 = document.createElement("input");
    dat_ot_wib_date1.type = "text";
    dat_ot_wib_date1.id = "dat_ot_wib_date1";
    div_ot_wib_date1.appendChild(dat_ot_wib_date1);
    // var lab_ot_wib_date1 = document.createElement("div");
    // lab_ot_wib_date1.innerHTML = "Waktu Mulai";
    // lab_ot_wib_date1.className = "filterlabel";
    // document.getElementById("div_ot_wib_date1").appendChild(lab_ot_wib_date1);

    var reset_ot_wib_date1 = document.createElement("div");
    reset_ot_wib_date1.innerHTML = "Clear Waktu Mulai &uarr;";
    reset_ot_wib_date1.className = "filterlabel";
    reset_ot_wib_date1.onclick = function() {
      tail
        .DateTime("#dat_ot_wib_date1", {
          dateStart: 946684800000,
          dateEnd: 2871590400000,
          dateFormat: "YYYY-mm-dd",
          timeFormat: "HH:ii:ss",
          today: false,
          weekStart: 1,
          position: "left",
          closeButton: true,
          timeStepMinutes: 1,
          timeStepSeconds: 1,
        })
        .selectDate(2012, 0, 1, 0, 0, 0);
      tail.DateTime("#dat_ot_wib_date1").reload();
    };
    document
      .getElementById("div_ot_wib_date1")
      .appendChild(reset_ot_wib_date1);
    document.addEventListener("DOMContentLoaded", function() {
      tail
        .DateTime("#dat_ot_wib_date1", {
          dateStart: 946684800000,
          dateEnd: 2871590400000,
          dateFormat: "YYYY-mm-dd",
          timeFormat: "HH:ii:ss",
          today: false,
          weekStart: 1,
          position: "left",
          closeButton: true,
          timeStepMinutes: 1,
          timeStepSeconds: 1,
        })
        .selectDate(2012, 0, 1, 0, 0, 0);
      tail.DateTime("#dat_ot_wib_date1").reload();
      tail
        .DateTime("#dat_ot_wib_date2", {
          dateStart: 946684800000,
          dateEnd: 2871590400000,
          dateFormat: "YYYY-mm-dd",
          timeFormat: "HH:ii:ss",
          today: false,
          weekStart: 1,
          position: "left",
          closeButton: true,
          timeStepMinutes: 1,
          timeStepSeconds: 1,
        })
        .selectDate(2024, 0, 1, 0, 0, 0);
      tail.DateTime("#dat_ot_wib_date2").reload();
      filterFunc();
      dat_ot_wib_date1.onchange = function() {
        filterFunc();
      };
      dat_ot_wib_date2.onchange = function() {
        filterFunc();
      };
    });
    var div_ot_wib_date2 = document.createElement("div");
    div_ot_wib_date2.id = "div_ot_wib_date2";
    div_ot_wib_date2.className = "filterselect";
    document.getElementById("menu").appendChild(div_ot_wib_date2);
    dat_ot_wib_date2 = document.createElement("input");
    dat_ot_wib_date2.type = "text";
    dat_ot_wib_date2.id = "dat_ot_wib_date2";
    div_ot_wib_date2.appendChild(dat_ot_wib_date2);
    // var lab_ot_wib_date2 = document.createElement("div");
    // lab_ot_wib_date2.innerHTML = "Waktu Berakhir";
    // lab_ot_wib_date2.className = "filterlabel";
    // document.getElementById("div_ot_wib_date2").appendChild(lab_ot_wib_date2);
    var reset_ot_wib_date2 = document.createElement("div");
    reset_ot_wib_date2.innerHTML = "Clear Waktu Berakhir &uarr;";
    reset_ot_wib_date2.className = "filterlabel";
    reset_ot_wib_date2.onclick = function() {
      tail
        .DateTime("#dat_ot_wib_date2", {
          dateStart: 946684800000,
          dateEnd: 2871590400000,
          dateFormat: "YYYY-mm-dd",
          timeFormat: "HH:ii:ss",
          today: false,
          weekStart: 1,
          position: "left",
          closeButton: true,
          timeStepMinutes: 1,
          timeStepSeconds: 1,
        })
        .selectDate(2024, 0, 1, 0, 0, 0);
      tail.DateTime("#dat_ot_wib_date2").reload();
    };
    document
      .getElementById("div_ot_wib_date2")
      .appendChild(reset_ot_wib_date2);
    document
      .getElementById("menu")
      .appendChild(document.createElement("div"));

    //Depth Slider
    document.getElementById("menu").appendChild(document.createElement("div"));
    var div_dept = document.createElement("div");
    div_dept.id = "div_dept";
    div_dept.className = "slider";
    document.getElementById("menu").appendChild(div_dept);

    // Create the input fields for custom value entry
    var inputNumberDeptMin = document.createElement("input");
    inputNumberDeptMin.type = "number";
    inputNumberDeptMin.min = 0;
    inputNumberDeptMin.max = 800;
    inputNumberDeptMin.step = 1;
    inputNumberDeptMin.value = 0;
    inputNumberDeptMin.id = "input_dept_min";
    inputNumberDeptMin.className = "custom-input";
    inputNumberDeptMin.style.marginLeft = "5px"; // Add left margin

    var inputNumberDeptMax = document.createElement("input");
    inputNumberDeptMax.type = "number";
    inputNumberDeptMax.min = 0;
    inputNumberDeptMax.max = 800;
    inputNumberDeptMax.step = 1;
    inputNumberDeptMax.value = 800;
    inputNumberDeptMax.id = "input_dept_max";
    inputNumberDeptMax.className = "custom-input";
    inputNumberDeptMax.style.marginLeft = "5px"; // Add left margin

    // Create a container for the custom input fields
    var div_customDeptInputs = document.createElement("div");
    div_customDeptInputs.className = "custom-input-container";

    // Append the custom inputs to the container
    div_customDeptInputs.appendChild(inputNumberDeptMin);
    div_customDeptInputs.appendChild(inputNumberDeptMax);

    // Append the custom input container to the menu
    document.getElementById("menu").appendChild(div_customDeptInputs);

    // Create the reset button
    var reset_dept = document.createElement("div");
    reset_dept.style.marginLeft = "5px"; // Add left margin
    reset_dept.innerHTML = "Clear Depth Filter &uarr;";
    reset_dept.className = "filterlabel";
    reset_dept.onclick = function() {
      sel_dept.noUiSlider.reset();
      inputNumberDeptMin.value = 0; // Reset the input fields when the slider is reset
      inputNumberDeptMax.value = 800;
    };
    document.getElementById("menu").appendChild(reset_dept);

    // Create the slider for depth
    var sel_dept = document.getElementById("div_dept");
    noUiSlider.create(sel_dept, {
      connect: true,
      start: [0, 800],
      step: 1,
      format: wNumb({
        decimals: 0,
      }),
      range: {
        min: 0,
        max: 800,
      },
    });

    sel_dept.noUiSlider.on("update", function(values) {
      // Update the custom input fields when the slider value changes
      inputNumberDeptMin.value = values[0];
      inputNumberDeptMax.value = values[1];
      filterFunc(); // Assuming filterFunc is a function you've defined elsewhere
    });

    // Update the slider when the custom input values change
    inputNumberDeptMin.addEventListener('change', function() {
      sel_dept.noUiSlider.set([this.value, null]);
    });
    inputNumberDeptMax.addEventListener('change', function() {
      sel_dept.noUiSlider.set([null, this.value]);
    });



    // Magnitude Slider
    // Add the slider to the menu
    document.getElementById("menu").appendChild(document.createElement("div"));
    var div_mag = document.createElement("div");
    div_mag.id = "div_mag";
    div_mag.className = "slider";
    document.getElementById("menu").appendChild(div_mag);

    // Create the input fields for custom value entry
    var inputNumberMagMin = document.createElement("input");
    inputNumberMagMin.type = "number";
    inputNumberMagMin.min = 0.0;
    inputNumberMagMin.max = 10.0;
    inputNumberMagMin.step = 0.1;
    inputNumberMagMin.value = 0.0;
    inputNumberMagMin.id = "input_mag_min"; // Set an ID for later use
    inputNumberMagMin.className = "custom-input";
    inputNumberMagMin.addEventListener('change', updateMagnitudeSlider);
    inputNumberMagMin.style.marginLeft = "5px"; // Add left margin

    var inputNumberMagMax = document.createElement("input");
    inputNumberMagMax.type = "number";
    inputNumberMagMax.min = 0.0;
    inputNumberMagMax.max = 10.0;
    inputNumberMagMax.step = 0.1;
    inputNumberMagMax.value = 10.0;
    inputNumberMagMax.id = "input_mag_max"; // Set an ID for later use
    inputNumberMagMax.className = "custom-input";
    inputNumberMagMax.addEventListener('change', updateMagnitudeSlider);
    inputNumberMagMax.style.marginLeft = "5px"; // Add left margin

    // Append the custom inputs to the menu
    document.getElementById("menu").appendChild(inputNumberMagMin);
    document.getElementById("menu").appendChild(inputNumberMagMax);

    // Function to update the slider based on input values
    function updateMagnitudeSlider() {
      sel_mag.noUiSlider.set([inputNumberMagMin.value || null, inputNumberMagMax.value || null]);
    }

    // Create the button for resetting the slider
    var reset_mag = document.createElement("div");
    reset_mag.style.marginLeft = "5px"; // Add left margin
    reset_mag.innerHTML = "Clear Magnitude Filter &uarr;";
    reset_mag.className = "filterlabel";
    reset_mag.onclick = function() {
      sel_mag.noUiSlider.reset();
      inputNumberMagMin.value = 0.0; // Also reset the input fields
      inputNumberMagMax.value = 10.0;
    };
    document.getElementById("menu").appendChild(reset_mag);

    // Create the magnitude slider
    var sel_mag = document.getElementById("div_mag");
    noUiSlider.create(sel_mag, {
      connect: true,
      start: [0.0, 10.0],
      range: {
        min: 0.0,
        max: 10.0,
      },
    });

    // Event listener for the slider update
    sel_mag.noUiSlider.on("update", function(values) {
      document.getElementById("input_mag_min").value = values[0];
      document.getElementById("input_mag_max").value = values[1];
      filterFunc(); // Assuming filterFunc is a function you've defined elsewhere
    });



    //Longitude Slider
    document.getElementById("menu").appendChild(document.createElement("div"));
    var div_lon = document.createElement("div");
    div_lon.id = "div_lon";
    div_lon.className = "slider";
    document.getElementById("menu").appendChild(div_lon);

    // Create the input fields for custom value entry
    var inputNumberLonMin = document.createElement("input");
    inputNumberLonMin.type = "number";
    inputNumberLonMin.min = -180;
    inputNumberLonMin.max = 180;
    inputNumberLonMin.step = 0.1;
    inputNumberLonMin.value = -180;
    inputNumberLonMin.id = "input_lon_min"; // Set an ID for later use
    inputNumberLonMin.className = "custom-input";
    inputNumberLonMin.style.marginLeft = "5px"; // Add left margin

    var inputNumberLonMax = document.createElement("input");
    inputNumberLonMax.type = "number";
    inputNumberLonMax.min = -180;
    inputNumberLonMax.max = 180;
    inputNumberLonMax.step = 0.1;
    inputNumberLonMax.value = 180;
    inputNumberLonMax.id = "input_lon_max"; // Set an ID for later use
    inputNumberLonMax.className = "custom-input";
    inputNumberLonMax.style.marginLeft = "5px"; // Add left margin

    // Append the custom inputs to the menu
    document.getElementById("menu").appendChild(inputNumberLonMin);
    document.getElementById("menu").appendChild(inputNumberLonMax);

    // Create the reset button for the slider
    var reset_lon = document.createElement("div");
    reset_lon.style.marginLeft = "5px"; // Add left margin
    reset_lon.innerHTML = "Clear Longitude Filter &uarr;";
    reset_lon.className = "filterlabel";
    reset_lon.onclick = function() {
      sel_lon.noUiSlider.reset();
      inputNumberLonMin.value = -180; // Also reset the input fields
      inputNumberLonMax.value = 180;
    };
    document.getElementById("menu").appendChild(reset_lon);

    // Create the longitude slider
    var sel_lon = document.getElementById("div_lon");
    noUiSlider.create(sel_lon, {
      connect: true,
      start: [-180.0, 180.0],
      range: {
        min: -180.0,
        max: 180.0,
      },
    });

    // Event listener for the slider update
    sel_lon.noUiSlider.on("update", function(values) {
      document.getElementById("input_lon_min").value = values[0];
      document.getElementById("input_lon_max").value = values[1];
      filterFunc(); // Assuming filterFunc is a function you've defined elsewhere
    });

    // Update the slider when the input values change
    inputNumberLonMin.addEventListener('change', function() {
      sel_lon.noUiSlider.set([this.value, null]);
    });
    inputNumberLonMax.addEventListener('change', function() {
      sel_lon.noUiSlider.set([null, this.value]);
    });



    //Latitude Slider
    document.getElementById("menu").appendChild(document.createElement("div"));
    var div_lat = document.createElement("div");
    div_lat.id = "div_lat";
    div_lat.className = "slider";
    document.getElementById("menu").appendChild(div_lat);

    // Create the input fields for custom value entry
    var inputNumberLatMin = document.createElement("input");
    inputNumberLatMin.type = "number";
    inputNumberLatMin.min = -90;
    inputNumberLatMin.max = 90;
    inputNumberLatMin.step = 0.1;
    inputNumberLatMin.value = -90;
    inputNumberLatMin.id = "input_lat_min"; // Set an ID for later use
    inputNumberLatMin.className = "custom-input";
    inputNumberLatMin.style.marginLeft = "5px"; // Add left margin

    var inputNumberLatMax = document.createElement("input");
    inputNumberLatMax.type = "number";
    inputNumberLatMax.min = -90;
    inputNumberLatMax.max = 90;
    inputNumberLatMax.step = 0.1;
    inputNumberLatMax.value = 90;
    inputNumberLatMax.id = "input_lat_max"; // Set an ID for later use
    inputNumberLatMax.className = "custom-input";
    inputNumberLatMax.style.marginLeft = "5px"; // Add left margin

    // Append the custom inputs to the menu
    document.getElementById("menu").appendChild(inputNumberLatMin);
    document.getElementById("menu").appendChild(inputNumberLatMax);

    // Create the reset button for the slider
    var reset_lat = document.createElement("div");
    reset_lat.style.marginLeft = "5px"; // Add left margin
    reset_lat.innerHTML = "Clear Latitude Filter &uarr;";
    reset_lat.className = "filterlabel";
    reset_lat.onclick = function() {
      sel_lat.noUiSlider.reset();
      inputNumberLatMin.value = -90; // Also reset the input fields
      inputNumberLatMax.value = 90;
    };
    document.getElementById("menu").appendChild(reset_lat);

    // Create the latitude slider
    var sel_lat = document.getElementById("div_lat");
    noUiSlider.create(sel_lat, {
      connect: true,
      start: [-90.0, 90.0],
      range: {
        min: -90.0,
        max: 90.0,
      },
    });

    // Event listener for the slider update
    sel_lat.noUiSlider.on("update", function(values) {
      document.getElementById("input_lat_min").value = values[0];
      document.getElementById("input_lat_max").value = values[1];
      filterFunc(); // Assuming filterFunc is a function you've defined elsewhere
    });

    // Update the slider when the input values change
    inputNumberLatMin.addEventListener('change', function() {
      sel_lat.noUiSlider.set([this.value, null]);
    });
    inputNumberLatMax.addEventListener('change', function() {
      sel_lat.noUiSlider.set([null, this.value]);
    });
  </script>
</body>

</html>