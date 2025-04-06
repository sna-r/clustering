from flask import Flask, request, jsonify
import pymysql
from flask_cors import CORS
from dotenv import load_dotenv
import os

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

    query = "SELECT * FROM users WHERE username = %s AND password = %s"
    try:
        # Open a new connection for this request
        db = pymysql.connect(**db_config)
        cursor = db.cursor()

        # Execute the query
        cursor.execute(query, (username, password))
        result = cursor.fetchall()

        # Close the connection
        cursor.close()
        db.close()

        if result:
            return jsonify({"message": "Login successful!"})
        else:
            return jsonify({"message": "Invalid credentials"}), 401
    except Exception as e:
        return jsonify({"error": f"MySQL Error: {e}"}), 500

if __name__ == '__main__':
    app.run(port=3000, debug=True)