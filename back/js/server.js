const express = require('express');
const mysql = require('mysql');
const bodyParser = require('body-parser');
const cors = require('cors');
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '..', '..', 'conf', '.env') });
const haproxyManager = require('./haproxy_manager');

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

app.post('/add-user', (req, res) => {
    const { username, password } = req.body;

    if (!username || !password) {
        return res.status(400).json({ message: 'Username and password are required' });
    }

    // Create a new connection for this request
    const db = mysql.createConnection(dbConfig);

    // Open the connection
    db.connect((err) => {
        if (err) {
            console.error("Database Connection Error:", err.message);
            return res.status(500).json({ message: 'Database connection error', error: err.message });
        }

        const query = 'INSERT INTO users (username, password) VALUES (?, ?)';
        db.query(query, [username, password], (err, results) => {
            // Close the connection
            db.end();

            if (err) {
                console.error("Database Query Error:", err.message);
                return res.status(500).json({ message: 'Database error', error: err.message });
            }

            res.json({ message: 'User added successfully!' });
        });
    });
});

// New /update-haproxy endpoint
app.post('/update-haproxy', async (req, res) => {
    try {
        // Call HAProxy management functions
        //let servers = haproxyManager.parseHaproxyCfg();
        //servers = await haproxyManager.updateServerStatuses(servers);
        //haproxyManager.saveServers(servers);
        //haproxyManager.generateHaproxyCfg(servers);
        haproxyManager.parseHaproxyCfg();
        res.json({ message: 'HAProxy configuration updated successfully.' });
    } catch (error) {
        res.status(500).json({ error: `Failed to update HAProxy: ${error.message}` });
    }
});

// Start Server
app.listen(port, '0.0.0.0',() => {
    console.log(`Server running on http://localhost:${port}`);
});