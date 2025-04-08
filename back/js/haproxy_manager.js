const fs = require('fs');
const net = require('net');
const { exec } = require('child_process');

// Path to HAProxy config and JSON file
const HAPROXY_CFG = '../../data/haproxy.cfg';
const JSON_FILE = '../../data/servers.json';

const source = '/etc/haproxy/haproxy.cfg'; // Path to the source file
const destination = '../../data/haproxy.cfg'; // Path to the destination file

// Function to copy a file using the `cp` command
function copyFile(sourcePath, destinationPath) {
    const command = `cp ${sourcePath} ${destinationPath}`;

    exec(command, (error, stdout, stderr) => {
        if (error) {
            console.error(`Error executing command: ${error.message}`);
            return;
        }
        if (stderr) {
            console.error(`Command error: ${stderr}`);
            return;
        }
        console.log(`File copied successfully: ${stdout}`);
    });
}

// Function to parse haproxy.cfg
async function parseHaproxyCfg() {
    //const servers = { webservers: [], mysql_masters: [], mysql_slaves: [] };
    //let currentBackend = null;

    copyFile(source,destination);

     // Initialize data structures
     const serversByType = {};
     let currentBackend = null;
 
     // Read and parse the haproxy.cfg file
     const lines = fs.readFileSync(HAPROXY_CFG, 'utf8').split('\n');
     lines.forEach(line => {
         line = line.trim();
         if (line.startsWith("backend")) {
             // Extract the backend name
             currentBackend = line.split(' ')[1];
         } else if (line.startsWith("server") && currentBackend) {
             // Parse server details
             const parts = line.split(' ');
             const serverName = parts[1];
             const [ip, port] = parts[2].split(':');
             const isBackup = line.includes("backup"); // Check if the server is marked as backup
             
             // Determine the server type based on the backend name
             let serverType;
             if (currentBackend === "webservers") {
                 serverType = "web server";
             } else if (currentBackend === "mysql_servers") {
                 serverType = "database server";
             } else {
                 serverType = "unknown";
             }
 
             // Create or update the server list for this type
             if (!serversByType[serverType]) {
                 serversByType[serverType] = [];
             }
 
             // Append server details to the list
             serversByType[serverType].push({
                 name: serverName,
                 ip: ip,
                 port: parseInt(port),
                 status: "unknown",           // Default status
                 password: "",                // Default password (empty)
                 backup: isBackup ? "Yes" : "No"  // Backup status
             });
         }
     });
 
     await updateServerStatuses(serversByType);

     // Write the parsed data to a JSON file
     fs.writeFileSync(JSON_FILE, JSON.stringify(serversByType, null, 4));
     console.log(`JSON file has been created: ${JSON_FILE}`);
}

// Function to save servers to JSON
function saveServers(servers) {
    fs.writeFileSync(JSON_FILE, JSON.stringify(servers, null, 4));
}

// Function to check server status
function checkServerStatus(ip, port) {
    return new Promise((resolve) => {
        const socket = new net.Socket();
        socket.setTimeout(5000);
        socket.connect(port, ip, () => {
            socket.destroy();
            resolve(true);
        });
        socket.on('error', () => {
            socket.destroy();
            resolve(false);
        });
        socket.on('timeout', () => {
            socket.destroy();
            resolve(false);
        });
    });
}

// Function to update server statuses
async function updateServerStatuses(servers) {
    for (const backendType in servers) {
        for (const server of servers[backendType]) {
            const isOnline = await checkServerStatus(server.ip, server.port);
            server.status = isOnline ? 'online' : 'offline';
            server.lastChecked = new Date().toISOString();
        }
    }
    return servers;
}

// Function to generate a new haproxy.cfg
function generateHaproxyCfg(servers) {
    const lines = fs.readFileSync(HAPROXY_CFG, 'utf-8').split('\n');
    const newLines = [];
    let currentBackend = null;

    lines.forEach(line => {
        const trimmedLine = line.trim();
        if (trimmedLine.startsWith('backend')) {
            currentBackend = trimmedLine.split(' ')[1];
            newLines.push(line);
        } else if (trimmedLine.startsWith('server')) {
            const serverName = trimmedLine.split(' ')[1];
            const backendServers = servers[currentBackend] || [];
            const server = backendServers.find(s => s.name === serverName);
            if (server) {
                const status = server.status === 'online' ? 'check' : 'disabled';
                newLines.push(`    server ${server.name} ${server.ip}:${server.port} ${status}`);
            } else {
                newLines.push(line);
            }
        } else {
            newLines.push(line);
        }
    });

    fs.writeFileSync(HAPROXY_CFG, newLines.join('\n'));
}

module.exports = {
    parseHaproxyCfg,
    updateServerStatuses,
    saveServers,
    generateHaproxyCfg
};