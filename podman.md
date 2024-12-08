##### copy conf avy ao amin'ilay container 
```bash
	#anaran'ilay container no atao ohatra oe mysql ilay cont
	podman cp mysql:/etc/my.cnf [toerana/ametrahana/ilay/izy]
	podman cp [toerana/misy/azy] mysql:/etc/my.cnf
	#exemple an'ilay izy fotsiny io 
	
```
micreer network raha tsy misy
```bash
	podman network ls
	podman network create [nom an'ilay izy]
	
```
##### manao ilay container vaovao 
```bash
	#podman run --name [anaran-ilay izy] -e MYSQL_ROOT_PASSWORD=mot de passe -d -p [port tiana]:3306 docker.io/library/mysql
	podman run --name mysql1 -e MYSQL_ROOT_PASSWORD=Admin1234 -d -p 9001:3306 docker.io/library/mysql
	podman run --name master1 -e MARIADB_ROOT_PASSWORD=Admin1234 -d -p 9001:3306 docker.io/library/mariadb:10.11.6
	podman run --network [network] --name master1 -e MARIADB_ROOT_PASSWORD=Admin1234 -d -p 9001:3306 docker.io/library/mariadb:10.11.6
	#ilay [network] tonga dia avaka atao ny nom an'ilay driver
```

##### ilay conf atao anatin'ilay my.cnf [mysqld]
```ini
	 [mysqld] 
	 bind-address = 0.0.0.0
	 server-id = 1 
	 log_bin = mysql-bin
	 binlog_format=ROW    
	auto_increment_offset=1  
	auto_increment_increment=2

# offset=2 ny serv2
```
##### manao ilay user ao anatin'ilay server 
```bash
	#miditra anatin'ilay mysql 
	#podman exec -it [anaran'ilay container] [ilay command]
	podman exec -it mysql mysql -u root -p
```

```bash
	mysql>> CREATE USER 'replicator'@'%' IDENTIFIED BY 'password';
GRANT REPLICATION SLAVE ON *.* TO 'replicator'@'%';
FLUSH PRIVILEGES;
```
mijery ilay master , hijerena ilay file log sy ny position
```sql
	SHOW MASTER STATUS;
```
mamorona slave amin'izay
```sql
	STOP SLAVE;
	CHANGE MASTER TO 
	MASTER_HOST = 'master2_ip', 
	MASTER_USER = 'replica_user', 
	MASTER_PORT = 3306,
	MASTER_PASSWORD = 'replica_password', 
	MASTER_LOG_FILE = 'mysql-bin.000003', -- File from Master2 
	MASTER_LOG_POS = 234; -- Position from Master2 START SLAVE;
```
mijery status :
```sql
SHOW SLAVE STATUS\G
```
