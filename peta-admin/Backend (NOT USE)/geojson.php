<?php
// Enable error reporting for debugging - disable error reporting in a production environment
error_reporting(E_ALL);
ini_set('display_errors', 1);

// MySQL connection parameters
$host = 'localhost';
$user = 'root';
$password = 'andisa8171';
$database = 'gempabumi';

// Create a new mysqli connection instance
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query
$sql = 'SELECT ot_wib, lon, lat, mag, dept, kota_terdekat, ket FROM gempa';
$result = $conn->query($sql);

$features = [];

while ($row = $result->fetch_assoc()) {
    // Format the ot_wib to match the JavaScript output
    $formattedDate = date('Y-m-d\TH:i:s.000', strtotime($row['ot_wib']));

    $features[] = [
        'type'       => 'Feature',
        'properties' => [
            'ot_wib'         => $formattedDate,
            'lon'            => (float) $row['lon'],
            'lat'            => (float) $row['lat'],
            'mag'            => (float) $row['mag'],
            'dept'           => (float) $row['dept'],
            'kota_terdekat'  => $row['kota_terdekat'],
            'ket'            => $row['ket'],
        ],
        'geometry'   => [
            'type'        => 'Point',
            'coordinates' => [
                (float) $row['lon'],
                (float) $row['lat']
            ],
        ],
    ];
}

$geojson = [
    'type'     => 'FeatureCollection',
    'name'     => 'Pointsfromtable_1',
    'crs'      => [
        'type' => 'name',
        'properties' => [
            'name' => 'urn:ogc:def:crs:OGC:1.3:CRS84'
        ],
    ],
    'features' => $features,
];

// Set the header to indicate JSON content
header('Content-Type: application/json');

// Output the GeoJSON object as JSON
echo json_encode($geojson, JSON_PRETTY_PRINT); // Use JSON_PRETTY_PRINT for readable output

// Close the MySQL connection
$conn->close();
