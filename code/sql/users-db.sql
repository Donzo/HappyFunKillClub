CREATE DATABASE IF NOT EXISTS yourDB;
USE yourDB;
CREATE TABLE users(
    account VARCHAR(50) NOT NULL UNIQUE,
    signature  VARCHAR(150) NOT NULL,
    pvpMatches INT DEFAULT 0,
    pvpWin INT DEFAULT 0,
    pvpLose INT DEFAULT 0,
    soloMatches INT DEFAULT 0,
    soloWin INT DEFAULT 0,
    soloLose INT DEFAULT 0,
    deck1 JSON,
    deck2 JSON,
    deck3 JSON,
    redCoins VARCHAR(8) DEFAULT '0',
	tkn VARCHAR(20),
    PRIMARY KEY (account)
);