<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$servername = "localhost";
$username = "root"; // replace with your username
$password = "andisa8171"; // replace with your password
$dbname = "gempabumi"; // replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT * FROM gempa ORDER BY ot_wib DESC LIMIT 1"; // Select all columnsc
$result = $conn->query($sql);

$geojson = array('type' => 'FeatureCollection', 'features' => array());

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $feature = array(
            'type' => 'Feature',
            'geometry' => array(
                'type' => 'Point',
                'coordinates' => array((float)$row['lon'], (float)$row['lat'])
            ),
            'properties' => array(
                'ot_wib' => $row['ot_wib'],
                'lon' => (float)$row['lon'],
                'lat' => (float)$row['lat'],
                'mag' => (float)$row['mag'],
                'dept' => $row['dept'],
                'kota_terdekat' => $row['kota_terdekat'],
                'ket' => $row['ket']
            )
        );
        array_push($geojson['features'], $feature);
    }
}

$conn->close();

echo json_encode($geojson);
