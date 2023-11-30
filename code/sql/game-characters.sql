CREATE TABLE gameCharacters (
	character_id INT AUTO_INCREMENT PRIMARY KEY,
	game_id INT,
	player ENUM('p1', 'p2'),
	card_id VARCHAR(4),
	card_name VARCHAR(35),
	health TINYINT UNSIGNED,
	energy TINYINT UNSIGNED,
	aim TINYINT UNSIGNED,
	speed TINYINT UNSIGNED,
	defend TINYINT UNSIGNED,
	luck TINYINT UNSIGNED,
	
	h_status VARCHAR(10),
	hs_int TINYINT UNSIGNED,
	t_status VARCHAR(10),
	v_status VARCHAR(10),
	
	a1_name VARCHAR(35),
	a1_type VARCHAR(10),
	a1_trait VARCHAR(10),
	a1_effect TINYINT UNSIGNED,
	a1_cost TINYINT UNSIGNED,
	
	a2_name VARCHAR(35),
	a2_type VARCHAR(10),
	a2_trait VARCHAR(10),
	a2_effect TINYINT UNSIGNED,
	a2_cost TINYINT UNSIGNED,
	
	a3_name VARCHAR(35),
	a3_type VARCHAR(10),
	a3_trait VARCHAR(10),
	a3_effect TINYINT UNSIGNED,
	a3_cost TINYINT UNSIGNED,
	
	location TINYINT UNSIGNED DEFAULT 0,
	
	INDEX idx_game_id (game_id),
	INDEX idx_player (player),
	INDEX idx_card_id (card_id),
	
	FOREIGN KEY (game_id) REFERENCES games(game_id) ON DELETE CASCADE
);