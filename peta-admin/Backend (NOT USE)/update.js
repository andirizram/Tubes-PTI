const express = require('express');
const mysql = require('mysql2');
const cors = require('cors');
const fs = require('fs');
const axios = require('axios');

const app = express();
app.use(cors());

// MySQL connection
const db = mysql.createConnection({
  host: 'localhost',
  user: 'root',
  password: 'andisa8171',
  database: 'gempabumi'
});

// Endpoint to close the server
app.get('/close', (req, res) => {
    res.send("Closing server...");
    server.close(() => {
      console.log('Server closed.');
    });
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

  async function fetchData() {
    try {
      const response = await axios.get('http://localhost:3000/geojson');
      const data = response.data;
  
      // Specify the path to the folder where you want to save the file
      const outputPath = '../data/Pointsfromtable_1.js';
  
      // Write the data to a file
      fs.writeFile(outputPath, `var json_Pointsfromtable_1 = ${JSON.stringify(data, null, 2)};`, (err) => {
          if (err) throw err;
          console.log('Data successfully written to file');
  
          // Wait for 2 seconds before closing the server
          setTimeout(() => {
            server.close(() => {
              console.log('Server closed after fetchData completed.');
            });
          }, 2000);
      });
    } catch (error) {
      console.error('Error fetching data:', error);
    }
  }

// Start the server
const PORT = 3000;
const server = app.listen(PORT, () => {
  console.log(`Server is running on http://localhost:${PORT}`);
  
  // Call fetchData function immediately or set a timeout based on your needs
  fetchData();
});
