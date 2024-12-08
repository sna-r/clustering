CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL
);
insert into users values (default,'Sitraka','test');
insert into users values (default,'Ny Avo','test');



