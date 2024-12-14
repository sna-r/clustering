CHANGE MASTER TO
    MASTER_HOST='172.50.100.162',
    MASTER_USER='slave_user',
    MASTER_PASSWORD='slave',
    MASTER_LOG_FILE='mysql-bin.000003',  -- Use the File from Master
    MASTER_LOG_POS=328;               -- Use the Position from Master

CREATE DATABASE test_db;

-- Use the new database
USE test_db;

-- Create a table named 'employees'
CREATE TABLE employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    position VARCHAR(50) NOT NULL,
    salary DECIMAL(10, 2) NOT NULL,
    hire_date DATE DEFAULT CURRENT_DATE
);

-- Insert test data into the 'employees' table
INSERT INTO employees (name, position, salary)
VALUES
    ('Alice Johnson', 'Software Engineer', 70000),
    ('Bob Smith', 'Data Analyst', 60000),
    ('Carol White', 'Project Manager', 80000),
    ('David Brown', 'UX Designer', 65000);

-- Retrieve all data from the 'employees' table to verify insertion
SELECT * FROM employees;



[mysqld]
server-id = 1
log_bin = mysql-bin
binlog_do_db = your_database_name
auto_increment_increment = 2
auto_increment_offset = 1
binlog_format=ROW                # Recommended for replication
auto_increment_offset=1          # For auto-increment conflict resolution
auto_increment_increment=2       # To avoid conflicts
gtid_domain_id=1                     # Unique GTID domain for each server
log_slave_updates                    # Ensures changes replicated to this server are logged

CREATE USER 'master'@'%' IDENTIFIED BY 'master';
GRANT REPLICATION SLAVE ON *.* TO 'master'@'%';
FLUSH PRIVILEGES;


-- server master1
CHANGE MASTER TO
  MASTER_HOST = 'master2',
  MASTER_PORT = 9002,
  MASTER_USER = 'master',
  MASTER_PASSWORD = 'master',
  MASTER_LOG_FILE = 'mysql-bin.000003',  
  MASTER_LOG_POS = 358;                  
START SLAVE;

-- server master2
CHANGE MASTER TO
  MASTER_HOST = 'master1',
  MASTER_PORT = 9001,
  MASTER_USER = 'master',
  MASTER_PASSWORD = 'master',
  MASTER_LOG_FILE = 'mysql-bin.000001',   
  MASTER_LOG_POS = 783;                   
START SLAVE;

-- server slave
CHANGE MASTER TO
  MASTER_HOST = '172.10.188.214',
  MASTER_PORT = 9004,
  MASTER_USER = 'master',
  MASTER_PASSWORD = 'master',
  MASTER_LOG_FILE = 'mysql-bin.000001',   
  MASTER_LOG_POS = 328;                    
START SLAVE;

-- server pc 
CHANGE MASTER TO
  MASTER_HOST = '127.0.0.1',
  MASTER_PORT = 7777,
  MASTER_USER = 'master',
  MASTER_PASSWORD = 'master',
  MASTER_LOG_FILE = 'mysql-bin.000002',   
  MASTER_LOG_POS = 328;                   
START SLAVE;

-- server podman master
CHANGE MASTER TO
  MASTER_HOST = '172.10.188.214',
  MASTER_PORT = 3307,
  MASTER_USER = 'master',
  MASTER_PASSWORD = 'master',
  MASTER_LOG_FILE = 'mysql-bin.000019',   
  MASTER_LOG_POS = 342;                    
START SLAVE;