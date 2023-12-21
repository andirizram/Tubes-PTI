const fs = require('fs');
const axios = require('axios');

// Function to fetch data and save it to a file
async function fetchData() {
  try {
    const response = await axios.get('http://localhost:3000/geojson');
    const data = response.data;

    // Specify the path to the folder where you want to save the file
    const outputPath = '../data/Pointsfromtable_1.js';

    // Write the data to a file
    fs.writeFile(outputPath, `var json_Pointsfromtable_1 = ${JSON.stringify(data, null, 2)};`, async (err) => {
      if (err) throw err;
      console.log('Data successfully written to file');

      // Request to shut down the server after writing the file
      // try {
      //   await axios.post('http://localhost:3000/shutdown');
      //   // console.log('Shutdown request sent.');
      // } catch (error) {
      //   // console.error('Error sending shutdown request:', error);
      // }
    });
  } catch (error) {
    // console.error('Error fetching data:', error);
  }
}

// Execute the function
fetchData();
