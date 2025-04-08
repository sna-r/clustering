import os
import json
import socket
import subprocess
from datetime import datetime

# Path to HAProxy config and JSON file
HAPROXY_CFG = "../../data/haproxy.cfg"
JSON_FILE = "../../data/servers.json"

source = '/etc/haproxy/haproxy.cfg'  # Path to the source file
destination = '../../data/haproxy.cfg'  # Path to the destination file

# Function to copy a file using the `cp` command
def copy_file(source_path, destination_path):
    command = ['cp', source_path, destination_path]

    try:
        result = subprocess.run(command, check=True, text=True, capture_output=True)
        print(f"File copied successfully: {result.stdout}")
    except subprocess.CalledProcessError as e:
        print(f"Error executing command: {e.stderr}")

# Function to parse haproxy.cfg
def parse_haproxy_cfg():
    # servers = {"webservers": [], "mysql_masters": [], "mysql_slaves": []}
    # current_backend = None

    servers_by_type = {}
    current_backend = None
    
    copy_file(source, destination)

     # Parse the haproxy.cfg file
    with open(HAPROXY_CFG, "r") as file:
        for line in file:
            line = line.strip()
            if line.startswith("backend"):
                # Extract the backend name
                current_backend = line.split()[1]
            elif line.startswith("server") and current_backend:
                # Parse server details
                parts = line.split()
                server_name = parts[1]
                server_ip_port = parts[2]
                ip, port = server_ip_port.split(":")
                is_backup = "backup" in line  # Check if the server is marked as backup
                
                # Determine the server type based on the backend name
                if current_backend == "webservers":
                    server_type = "web server"
                elif current_backend == "mysql_servers":
                    server_type = "database server"
                else:
                    server_type = "unknown"

                # Create or update the server list for this type
                if server_type not in servers_by_type:
                    servers_by_type[server_type] = []
                
                # Append server details to the list
                servers_by_type[server_type].append({
                    "name": server_name,
                    "ip": ip,
                    "port": int(port),
                    "status": "unknown",          # Default status
                    "password": "",               # Default password (empty)
                    "backup": "Yes" if is_backup else "No"  # Backup status
                })

    servers_by_type = update_server_statuses(servers_by_type)

    # Write the parsed data to a JSON file
    with open(JSON_FILE, "w") as jsonfile:
        json.dump(servers_by_type, jsonfile, indent=4)

    print(f"JSON file has been created: {JSON_FILE}")

# Function to save servers to JSON
def save_servers(servers):
    with open(JSON_FILE, "w") as file:
        json.dump(servers, file, indent=4)

# Function to check server status
def check_server_status(ip, port):
    try:
        sock = socket.socket(socket.AF_INET, socket.SOCK_STREAM)
        sock.settimeout(5)
        result = sock.connect_ex((ip, port))
        sock.close()
        return result == 0
    except Exception as e:
        print(f"Error checking {ip}:{port}: {e}")
        return False

# Function to update server statuses
def update_server_statuses(servers):
    for backend_type, backend_servers in servers.items():
        for server in backend_servers:
            ip = server["ip"]
            port = server["port"]
            is_online = check_server_status(ip, port)
            server["status"] = "online" if is_online else "offline"
            server["last_checked"] = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    return servers

# Function to generate a new haproxy.cfg
def generate_haproxy_cfg(servers):
    with open(HAPROXY_CFG, "r") as file:
        lines = file.readlines()

    new_lines = []
    current_backend = None

    for line in lines:
        stripped_line = line.strip()
        if stripped_line.startswith("backend"):
            current_backend = stripped_line.split()[1]
            new_lines.append(line)
        elif stripped_line.startswith("server"):
            server_name = stripped_line.split()[1]
            backend_servers = servers.get(current_backend, [])
            server = next((s for s in backend_servers if s["name"] == server_name), None)
            if server:
                status = "check" if server["status"] == "online" else "disabled"
                new_line = f"    server {server['name']} {server['ip']}:{server['port']} {status}\n"
                new_lines.append(new_line)
        else:
            new_lines.append(line)

    with open(HAPROXY_CFG, "w") as file:
        file.writelines(new_lines)