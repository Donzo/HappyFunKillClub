CREATE DATABASE IF NOT EXISTS dbs12078668;
USE dbs12078668;
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
	coinsMinted BOOLEAN DEFAULT FALSE,
	tkn VARCHAR(20),
    PRIMARY KEY (account)
);