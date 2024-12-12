podman run --network bridge \
--name master1 \
-e MARIADB_ROOT_PASSWORD=Admin1234 -d \
-p 9001:3306 \
-v ~/Documents/cluster-pod/master1:/var/lib/mysql \
docker.io/library/mariadb:10.11.6 > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "master1 container already exists. Starting the existing container..."
    podman start master1
fi

podman run --network bridge \
--name master2 \
-e MARIADB_ROOT_PASSWORD=Admin1234 -d \
-p 9002:3306 \
-v ~/Documents/cluster-pod/master2:/var/lib/mysql \
docker.io/library/mariadb:10.11.6 > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "master2 container already exists. Starting the existing container..."
    podman start master2
fi

podman run --network bridge \
--name slave1 \
-e MARIADB_ROOT_PASSWORD=Admin1234 -d \
-p 9003:3306 \
-v ~/Documents/cluster-pod/slave1:/var/lib/mysql \
docker.io/library/mariadb:10.11.6 > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "slave1 container already exists. Starting the existing container..."
    podman start slave1
fi

podman run --network bridge \
--name slave2 \
-e MARIADB_ROOT_PASSWORD=Admin1234 -d \
-p 9004:3306 \
-v ~/Documents/cluster-pod/slave2:/var/lib/mysql \
docker.io/library/mariadb:10.11.6 > /dev/null 2>&1

if [ $? -ne 0 ]; then
    echo "slave2 container already exists. Starting the existing container..."
    podman start slave2
fi

podman exec -it master1 touch /var/log/mysql/mysql-bin.log
podman exec -it master2 touch /var/log/mysql/mysql-bin.log
podman exec -it slave1 touch /var/log/mysql/mysql-bin.log
podman exec -it slave2 touch /var/log/mysql/mysql-bin.log
podman cp ~/Documents/cluster-pod/master1.cnf master1:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/master2.cnf master2:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/slave1.cnf slave1:/etc/mysql/mariadb.cnf
podman cp ~/Documents/cluster-pod/slave2.cnf slave2:/etc/mysql/mariadb.cnf
