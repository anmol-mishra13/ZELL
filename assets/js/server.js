const express = require('express');
const bodyParser = require('body-parser');
const mysql = require('mysql');
const cors = require('cors'); // Import cors

const app = express();
const port = 3000;

// Middleware
app.use(cors()); // Enable CORS
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static('public')); // Serve static files from the public directory

// MySQL connection
const db = mysql.createConnection({
    host: 'localhost',
    user: 'postgres', // Replace with your MySQL username
    password: 'password', // Replace with your MySQL password
    database: 'zell_education' // Replace with your database name
});

db.connect((err) => {
    if (err) {
        console.error('Database connection error: ', err);
        return;
    }
    console.log('Connected to the MySQL database.');
});

// Route to handle form submissions
app.post('/submit-form', (req, res) => {
  const { userType, name, email, qualification, university, guardianNumber, designation, currentCTC, currentCompany } = req.body;

  // Query to insert the form data
  let sql = '';
  if (userType === 'student') {
      sql = 'INSERT INTO students (name, email, qualification, university, guardian_number) VALUES (?, ?, ?, ?, ?)';
  } else {
      sql = 'INSERT INTO professionals (name, email, designation, current_ctc, current_company) VALUES (?, ?, ?, ?, ?)';
  }

  const values = userType === 'student' 
      ? [name, email, qualification, university, guardianNumber] 
      : [name, email, designation, currentCTC, currentCompany];

  db.query(sql, values, (err, result) => {
      if (err) {
          console.error('Error inserting data: ', err);
          return res.status(500).json({ message: 'Database error' });
      }
      res.json({ message: 'Form submitted successfully!', id: result.insertId });
  });
});


app.listen(port, () => {
    console.log(`Server is running on http://localhost:${port}`);
});
