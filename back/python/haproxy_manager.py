import os
import json
import socket
from datetime import datetime

# Path to HAProxy config and JSON file
HAPROXY_CFG = "haproxy.cfg"
JSON_FILE = "servers.json"

# Function to parse haproxy.cfg
def parse_haproxy_cfg():
    servers = {"webservers": [], "mysql_masters": [], "mysql_slaves": []}
    current_backend = None

    with open(HAPROXY_CFG, "r") as file:
        for line in file:
            line = line.strip()
            if line.startswith("backend"):
                current_backend = line.split()[1]
            elif line.startswith("server"):
                parts = line.split()
                server_name = parts[1]
                server_ip_port = parts[2]
                ip, port = server_ip_port.split(":")
                servers[current_backend].append({
                    "name": server_name,
                    "ip": ip,
                    "port": int(port),
                    "status": "unknown",
                    "last_checked": None
                })
    return servers

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