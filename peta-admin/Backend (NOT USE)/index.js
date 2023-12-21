const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');

const app = express();
app.use(cors());

// MySQL connection
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: 'andisa8171',
  database: 'gempabumi'
});

// Endpoint to get data from MySQL
app.get('/geojson', (req, res) => {
  const sql = 'SELECT ot_wib, lon, lat, mag, dept, kota_terdekat, ket FROM gempa';
  db.query(sql, (err, results) => {
    if (err) throw err;

    // Convert results to GeoJSON format
    const geojson = {
      type: "FeatureCollection",
      features: results.map(item => ({
        type: "Feature",
        properties: item,
        geometry: {
          type: "Point",
          coordinates: [item.lon, item.lat]
        }
      }))
    };

    res.json(geojson);
  });
});

// Start the server
const PORT = 3000;
const server = app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
});
