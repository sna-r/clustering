docker exec -it master1 touch /var/log/mysql/mysql-bin.log
docker exec -it master2 touch /var/log/mysql/mysql-bin.log
docker exec -it slave1 touch /var/log/mysql/mysql-bin.log
docker exec -it slave2 touch /var/log/mysql/mysql-bin.log

docker cp ~/Documents/cluster-pod/master1.cnf master1:/etc/mysql/mariadb.cnf
docker cp ~/Documents/cluster-pod/master2.cnf master2:/etc/mysql/mariadb.cnf
docker cp ~/Documents/cluster-pod/slave1.cnf slave1:/etc/mysql/mariadb.cnf
docker cp ~/Documents/cluster-pod/slave2.cnf slave2:/etc/mysql/mariadb.cnf