podman run -d --network bridge \
--name master1 \
-p 9001:3306 \
-v ~/Documents/cluster-pod/master1:/var/lib/mysql \
docker.io/library/mariadb:10.11.6

podman run -d --network bridge \
--name master2 \
-p 9002:3306 \
-v ~/Documents/cluster-pod/master2:/var/lib/mysql \
docker.io/library/mariadb:10.11.6

podman run -d --network bridge \
--name slave1 \
-p 9003:3306 \
-v ~/Documents/cluster-pod/slave1:/var/lib/mysql \
docker.io/library/mariadb:10.11.6

podman run -d --network bridge \
--name slave2 \
-p 9004:3306 \
-v ~/Documents/cluster-pod/slave2:/var/lib/mysql \
docker.io/library/mariadb:10.11.6

podman exec -it master1 chmod -R 777 /var/lib/mysql
podman exec -it master2 chmod -R 777 /var/lib/mysql
podman exec -it slave1 chmod -R 777 /var/lib/mysql
podman exec -it slave2 chmod -R 777 /var/lib/mysql

podman exec -it master1 touch /var/log/mysql/mysql-bin.log
podman exec -it master2 touch /var/log/mysql/mysql-bin.log
podman exec -it slave1 touch /var/log/mysql/mysql-bin.log
podman exec -it slave2 touch /var/log/mysql/mysql-bin.log

podman cp ~/Documents/cluster-pod/master1.cnf master1:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/master2.cnf master2:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/slave1.cnf slave1:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/slave2.cnf slave2:/etc/mysql/mariadb.cnf

podman exec -it master1 chmod -R 777 /var/lib/mysql
podman exec -it master2 chmod -R 777 /var/lib/mysql
podman exec -it slave1 chmod -R 777 /var/lib/mysql
podman exec -it slave2 chmod -R 777 /var/lib/mysql