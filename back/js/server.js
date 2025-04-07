const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '..', '..', 'conf', '.env') });

const app = express();
const port = 3000;

// Middleware
app.use(bodyParser.json());
app.use(cors());

// Load database configuration from .env
const dbConfig = {
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASSWORD,
    database: process.env.DB_NAME
};

// Login API
app.post('/login', (req, res) => {
    const { username, password } = req.body;

    // Create a new connection for this request
    const db = mysql.createConnection(dbConfig);

    // Open the connection
    db.connect((err) => {
        if (err) {
            return res.status(500).json({ message: 'Database connection error', error: err.message });
        }

        const query = 'SELECT * FROM users WHERE username = ? AND password = ?';
        db.query(query, [username, password], (err, results) => {
            // Close the connection
            db.end();

            if (err) {
                return res.status(500).json({ message: 'Database error' });
            }

            if (results.length > 0) {
                const user = results[0]; // Assuming usernames are unique
                res.json({
                    message: 'Login successful!',
                    user_id: user.id,
                    username: user.username
                });
            } else {
                res.json({ message: 'Invalid credentials' });
            }
        });
    });
});

// Start Server
app.listen(port, () => {
    console.log(`Server running on http://localhost:${port}`);
});