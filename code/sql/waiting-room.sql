CREATE DATABASE IF NOT EXISTS dbs12078668;
USE dbs12078668;
CREATE TABLE waitingRoom(
	id INT AUTO_INCREMENT PRIMARY KEY,
    account VARCHAR(50) NOT NULL UNIQUE,
    deck JSON,
    enteredAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);