CREATE DATABASE IF NOT EXISTS yourDB;
	USE yourDB;

CREATE TABLE games (
    game_id INT AUTO_INCREMENT PRIMARY KEY,
    creationDate TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    roundNumber SMALLINT UNSIGNED DEFAULT 1,
    lastRoundEnd TIMESTAMP,
    nextRoundEnd TIMESTAMP,
    p1DeckJSON JSON,
    p2DeckJSON JSON,
    p1WalletAddress VARCHAR(50),
    p2WalletAddress VARCHAR(50),
    status ENUM('WAITING', 'LOADING', 'READY', 'IN_PROGRESS', 'PROCESSING', 'FINISHED') DEFAULT 'WAITING',
	p1Moved BOOLEAN DEFAULT FALSE,
	p2Moved BOOLEAN DEFAULT FALSE,
	p1MovesJSON JSON,
    p2MovesJSON JSON,
	moveSummariesJSON JSON,
	actionSummariesJSON JSON,
	simpleMoveSummariesJSON JSON,
	simpleActionSummariesJSON JSON,
	p1ActionsJSON JSON,
    p2ActionsJSON JSON,
    valuesUpdated BOOLEAN DEFAULT FALSE,
    gameStartedBy VARCHAR(50)
);
