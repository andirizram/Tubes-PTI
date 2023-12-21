<?php
$url = 'http://localhost/PTI/peta-admin/geojson.php'; // The URL where your geojson.php script is located
$outputPath = 'data/Pointsfromtable_1.js'; // Adjust the path as necessary

// Fetch the GeoJSON data
$data = file_get_contents($url);
if ($data === false) {
    die("Could not fetch data.");
}

// Write the data to a file
if (file_put_contents($outputPath, "var json_Pointsfromtable_1 = $data;") === false) {
    die("Could not write to file.");
}

echo "Data successfully written to the file.<br>";
echo "<br>You can close this window.";
