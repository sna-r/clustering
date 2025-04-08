from flask import Flask, request, jsonify
import json
import pymysql
from flask_cors import CORS
from dotenv import load_dotenv
import os
from haproxy_manager import parse_haproxy_cfg, update_server_statuses, generate_haproxy_cfg, save_servers

# Load environment variables from .env file
dotenv_path = os.path.join(os.path.dirname(__file__), '..', '..', 'conf', '.env')
load_dotenv(dotenv_path)

app = Flask(__name__)
CORS(app)

# Load database configuration from .env
db_config = {
    'host': os.getenv('DB_HOST'),
    'user': os.getenv('DB_USER'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_NAME')
}

# Login API
@app.route('/login', methods=['POST'])
def login():
    data = request.get_json()
    username = data.get('username')
    password = data.get('password')

    query = "SELECT id,username FROM users WHERE username = %s AND password = %s"
    try:
        # Open a new connection for this request
        db = pymysql.connect(**db_config)
        cursor = db.cursor()

        # Execute the query
        cursor.execute(query, (username, password))
        result = cursor.fetchone()

        # Close the connection
        cursor.close()
        db.close()

        if result:
            # print(f"Query result: {result}")
            user_id, username = result
            return jsonify({
                "message": "Login successful!",
                "user_id": user_id,
                "username": username
            })
        else:
            return jsonify({"message": "Invalid credentials"}), 401
    except Exception as e:
        return jsonify({"error": f"MySQL Error: {e}"}), 500

# Adding user
@app.route('/add-user', methods=['POST'])
def add_user():
    data = request.get_json()
    username = data.get('username')
    password = data.get('password')

    if not username or not password:
        return jsonify({"message": "Username and password are required"}), 400

    query = "INSERT INTO users (username, password) VALUES (%s, %s)"
    try:
        # Open a new connection for this request
        db = pymysql.connect(**db_config)
        cursor = db.cursor()

        # Execute the query
        cursor.execute(query, (username, password))
        db.commit()  # Commit the transaction

        # Close the connection
        cursor.close()
        db.close()

        return jsonify({"message": "User added successfully!"})
    except Exception as e:
        return jsonify({"error": f"MySQL Error: {e}"}), 500


# New /update-haproxy endpoint
@app.route('/update-haproxy', methods=['POST'])
def update_haproxy():
    try:
        # Step 1: Parse haproxy.cfg
        # servers = parse_haproxy_cfg()
        parse_haproxy_cfg()

        # Step 2: Update server statuses
        # servers = update_server_statuses(servers)
        # save_servers(servers)

        # Step 3: Generate a new haproxy.cfg
        # generate_haproxy_cfg(servers)

        return jsonify({"message": "HAProxy configuration updated successfully."})
    except Exception as e:
        return jsonify({"error": f"Failed to update HAProxy: {e}"}), 500

# Load the JSON data from the file
def load_server_data(json_file_path):
    with open(json_file_path, "r") as file:
        return json.load(file)

# Endpoint to get all server data
@app.route('/servers', methods=['GET'])
def get_all_servers():
    json_file_path = "../../data/servers.json"  # Path to your JSON file
    servers = load_server_data(json_file_path)
    return jsonify(servers)

# Endpoint to get servers by type (e.g., "web server" or "database server")
@app.route('/servers/<server_type>', methods=['GET'])
def get_servers_by_type(server_type):
    json_file_path = "../../data/servers.json"  # Path to your JSON file
    servers = load_server_data(json_file_path)
    
    if server_type in servers:
        return jsonify(servers[server_type])
    else:
        return jsonify({"error": f"No servers found for type: {server_type}"}), 404

if __name__ == '__main__':
    app.run(host='0.0.0.0' , port=3000, debug=True)