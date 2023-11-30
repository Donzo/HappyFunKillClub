ig.module(
	'game.entities.characterpiece'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
	
	//Characters are being killed on spawn and living on as data objects.
	// Must figure out a way to resurrect them if that happens, also maybe figure out why they die.
	
EntityCharacterpiece=ig.Entity.extend({
	size: {x: 128, y: 128},
	offset: {x: 0, y: 64},
	maxVel: {x: 000, y: 000},
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	
	myCharacter: null,
	myTile: 0,
	myTileName: null,
	player: false,
	pieceName: false,
	characterNum: false,
	dboVar: false,
	killMeNow: false,
	hasMoved: false,
	hasActed: false,
	moveIndicatorSpawned: false,
	
	//Array
	cardData: null,
	
	selected: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 0.1)',
	animSheet: new ig.AnimationSheet( 'media/character-pieces.png', 128, 192 ),
	//openSound: new ig.Sound( 'media/sounds/open-01.*' ),
	killMeNow: false,
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);
		this.killMeTimer = new ig.Timer(0);

		this.addAnim( 'p1c1chill', 1, [0], true );
		this.addAnim( 'p2c1chill', 1, [2], true );
		this.addAnim( 'p1c1selectMe', 1, [0], true );
		this.addAnim( 'p2c1selectMe', 1, [2], true );
		this.addAnim( 'p1c2chill', 1, [8], true );
		this.addAnim( 'p2c2chill', 1, [10], true );
		this.addAnim( 'p1c2selectMe', 1, [8], true );
		this.addAnim( 'p2c2selectMe', 1, [10], true );
		this.addAnim( 'p1c3chill', 1, [16], true );
		this.addAnim( 'p2c3chill', 1, [18], true );
		this.addAnim( 'p1c3selectMe', 1, [16], true );
		this.addAnim( 'p2c3selectMe', 1, [18], true );
		
		//Do all these...
		this.addAnim( 'p1c1chillKill', .15, [0,4,26,4] );
		this.addAnim( 'p1c2chillKill', .15, [8,12,26,12] );
		this.addAnim( 'p1c3chillKill', .15, [16,20,26,20] );
		this.addAnim( 'p2c1chillKill', .15, [2,6,26,6]);
		this.addAnim( 'p2c2chillKill', .15, [10,14,26,14]);
		this.addAnim( 'p2c3chillKill', .15, [18,22,26,22]);
		this.hasActed = true;
		this.myCharacter = `p${this.player}` == ig.game.playerNumber ? true : false;
		this.establishMe();
	},
	reset: function( x, y, settings ) {
		this.parent( x, y, settings );
		this.myCharacter = `p${this.player}` == ig.game.playerNumber ? true : false;
		this.selected = false;
		this.killMeNow = false;
		this.establishMe();
		
		//ig.game.spawnEntity( EntityCharacterpiece, tile.pos.x, tile.pos.y, { player: pNum, characterNum: this.num, myTile: deployTile});

    },
    selectMe: function(){
    	ig.game.clearTileColors(); 
    	ig.game.unselectAllTiles();
    	ig.game.tileNumberSelected = false;
    	ig.game.deselectAllSquares(); 
		ig.game.lookingAtMyCharacter = this.myCharacter;
		
		var dboVar = `p${this.player}C${this.characterNum}data`;
		ig.game.selectedPieceDBOVar = dboVar;
		var pieceName = `ch${dboVar.character_id}`;
		ig.game.setCardDisplay(this.player, this.characterNum);
		ig.game.displayCard = true;
		ig.game.displayCardOnSquare = this.myTile;
		ig.game.displayCardView = 1;
		ig.game.playOpenSound();
		this.selected = true;
		ig.game.sPieceID = this.myID
		ig.game.pieceSelected = this.name;
		ig.game.sPieceName = this.name
		ig.game.sPieceCharacterNum = this.characterNum;
		this.myPiece = ig.game.playerNumber == `p${this.player}` ? true : false;
		this.maybeMoveCamera = true;
		if (!this.hasMoved && ig.game.lookingAtMyCharacter && this.myPiece && this.myCharacter){
			this.showMoves();
		}
    },
     unselectMe: function(){
    	ig.game.clearTileColors(); 
    	ig.game.tileNumberSelected = false;
    	ig.game.deselectAllSquares(); 
		ig.game.lookingAtMyCharacter = false;

		ig.game.displayCard = false;
		ig.game.displayCardOnSquare = false;
		ig.game.displayCardView = 1;
		ig.game.playCloseSound();
		this.selected = false;
		ig.game.sPieceID = false;
		ig.game.pieceSelected = false;
		ig.game.sPieceName = false;
		ig.game.sPieceCharacterNum = false;;
    },
    establishMe: function(){
    	this.nameMe();
    	var dboVar = `p${this.player}C${this.characterNum}data`;
    	this.mydboVar = dboVar;
		ig.game[dboVar].location = this.myTile;
		if (!ig.game.turnReporting && !ig.game.turnEnded){
			this.selectMe();
		}
		ig.game.charHealthBarsToDraw.push(this.mydboVar);
		//Spawn Indicator
		if (this.myCharacter && !this.moveIndicatorSpawned){
			this.moveIndicatorSpawned = true;
			ig.game.spawnEntity( EntityMoveindicator, 0, 0, { myCharacterName: this.name });	
		}
		
		
		//var dboVar = `p${this.player}C${this.characterNum}data`;
    },
    removeHealthBarAfterDeath: function() {
		// Find the index of the dboVar in the array
		const index = ig.game.charHealthBarsToDraw.indexOf(this.mydboVar);

		// If found (index > -1), remove the element from the array
		if (index > -1) {
			ig.game.charHealthBarsToDraw.splice(index, 1);
		}
	},
    setLocation: function(){
    	var tileName = "tn"+ this.myTile;
		var tile = ig.game.getEntityByName(tileName);					
		if (this.myTile == 86){
			this.killMe();
		}
		if (tile){
			this.pos.x = tile.pos.x;
			this.pos.y = tile.pos.y;
		}
		ig.game[this.mydboVar].myTile = this.myTile;
    },
	nameMe: function(){
		if (!this.otherPlayer){
			var dboVar = `p${this.player}C${this.characterNum}data`;
			this.name =  `ch${ig.game[dboVar].character_id}`;
			this.myID = ig.game[dboVar].character_id;
			//Set Character Number
			ig.game[dboVar].characterNum = this.characterNum;
		}
		ig.game.playDeployCharacterSound();
	},
	//This is called at the start of every new round.
	newRoundMe: function(){
		this.selected = false;
		this.hasMoved = false;
		this.hasActed = false;
		var dboVar = `p${this.player}C${this.characterNum}data`;
		this.myTile = ig.game[dboVar].location;
	},
	moveCameraIfCovered(){
		if (ig.game.cdbl + ig.game.screen.x < this.pos.x + (this.size.x * 2)){
			ig.game.xCamera = this.pos.x - (ig.system.width /2) + (this.size.x * 2);
		}

	},
	showMoves: function(){
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var eNum = ig.game.playerNumber == "p2" ? 1: 2;
		var deployTile = pNum == 1 ? 4 : 8;
		
		if (!this.hasMoved){
			ig.game.clearTileColors();
			
			//Highlight Adjacent Tile Colors
			this.myTile = ig.game[this.mydboVar].location; //Verify Location
			ig.game.highlightAdjacentTiles(this.myTile, "validLoc");
			
			//Deselect Player Tiles
			ig.game.deselectPlayerOccupiedTiles(this.myTile, pNum, ig.game.initialOccupiedTiles);
			
			//Clear My Tiles
			ig.game.clearOneTileColor(this.myTile);
			
			//Clear Deploy Tile
			ig.game.clearOneTileColor(deployTile);
			
			//Get Adjacent Tiles
			var adjacentTiles = ig.game.getAdjacentTiles(this.myTile);
			
			//Set Enemy (adjacent) Tiles Invalid
			var enemyOccupiedTiles = ig.game.getPieceLocationsOfEnemy();
			// Convert 'Enemy Occupied Tiles' to numbers
			var enemyOccupiedTilesAsNumbers = enemyOccupiedTiles.map(tile => parseInt(tile));

			// Find common tiles between adjacent and enemy-occupied tiles
			var commonTiles = adjacentTiles.filter(tile => enemyOccupiedTilesAsNumbers.includes(tile));
			
			//var commonTiles = adjacentTiles.filter(tile => enemyOccupiedTiles.includes(String(tile)));
			ig.game.setTileColorsOfArray(commonTiles, "invalidMove");
			
			//Remove Tiles where my other moves are attempted
			ig.game.deselectTilesWithAttemptedMovesOnThem();
			
			ig.game.lookingAtMoves = true;
		}
	},
	showActions: function(){
		if (!this.hasActed){
			//Highlight Adjacent Tile Colors
			ig.game.highlightAdjacentTiles(this.myTile, "validLoc");
			this.deselectPlayerOccupiedTiles();
			ig.game.clearOneTileColor(this.myTile);
			var adjacentTiles = ig.game.getAdjacentTiles(this.myTile);
			//Set colors of enemy occupied tiles to valid
			
			
			//meleeTarget  rangeTarget drainMeleeTarget drainRangeTarget
			
			
			//var enemyOccupiedTiles = ig.game.getPieceLocationsOfEnemy();
			//var commonTiles = adjacentTiles.filter(tile => enemyOccupiedTiles.includes(tile));
			//ig.game.setTileColorsOfArray(commonTiles, "invalidMove");
			//Remove Tiles where my other moves are attempted
			ig.game.deselectTilesWithAttemptedMovesOnThem();
			
			ig.game.lookingAtMoves = true;
		}
	},

	update: function() {
	
		if (this.killMeNow && this.killMeTimer.delta() > 0){
			this.myTile = 86;	
			this.myTileName = false;
			var dboVar = `p${this.player}C${this.characterNum}data`;
			ig.game[dboVar].location = 86;
			this.removeHealthBarAfterDeath();
			this.kill(1);
		}
		if (ig.game.transitioning || ig.game.turnEnded || ig.game.openButtonMenuDisplay == "characterCards" || ig.game.turnEnded || ig.game.turnReporting || ig.game.playerWon){
			this.ready = false;
		}
		else{
			this.ready = true;
		}
		
		this.setLocation();
		
		this.animateMe();
		
		if (this.maybeMoveCamera){
			this.moveCameraIfCovered();
			if (ig.game.cdbl){
				this.maybeMoveCamera = false;
			}
		}
		
		if (ig.game.actionCall && ig.game.actionType ){
			if (ig.game.actionType == "Melee" || ig.game.actionType == "Ranged" || ig.game.actionType == "Drain" || ig.game.actionType == "R Drain"  ){
				if (this.myCharacter){
					this.properActionType = false;
				}
				else{
					this.properActionType = true;
				}
			}
			else if (ig.game.actionType == "Boost" || ig.game.actionType == "Support" || ig.game.actionType == "R Support" ){
				if (this.myCharacter){
					this.properActionType = true;
				}
				else{
					this.properActionType = false;
				}
			}
		}
		else{
			this.properActionType = false;
		}
		
		//Click me
		//Handle Action Calls
		if (ig.input.released('click') && this.inFocus() && this.ready && ig.game.actionCall && this.properActionType){ //THIS IS HOW WE CALL ACTIONS
			//Action Attributes
			//ig.game.actionNum = actionNum;
			//ig.game.actionName = actionName;
			//ig.game.actionType = actionType;
			//ig.game.actionCost = actionCost;
			/*
			
			const actionNum = `action${this.num}`;
			const actionName = ig.game[actionNum];
			const actionType = ig.game[`${actionNum}type`];
			const √ = ig.game[`${actionNum}cost`];
			const hasActed = ig.game[`hasActed${this.num}`];
			
			ig.game.actionNum = actionNum;
			ig.game.actionName = actionName;
			ig.game.actionType = actionType;
			ig.game.actionCost = actionCost;
			*/
			var dboVar = ig.game.selectedPieceDBOVar;
			var actorID = `ch${ig.game[dboVar].character_id}`;
			//Target Attributes // This variable holds actors DBO ig.game.selectedPieceDBOVar;
			var tdboVar = `p${this.player}C${this.characterNum}data`; //This is the dbo of the target
			//Set Target for actor 
			ig.game[dboVar].action = ig.game.actionNum; //action: characterData.action,target: characterData.target,
			ig.game[dboVar].target = ig.game[tdboVar].character_id;
			
			var actorEnt = ig.game.getEntityByName(actorID);
			actorEnt.hasActed = true;
			
			var actorDBO = actorEnt.mydboVar 
			//Subtract Energy
			ig.game[actorDBO].energy -= ig.game.actionCost;
			
			ig.game.displayCard = false;
			ig.game.displayCardView = 1;
			ig.game.clearTileColors();
			ig.game.actionCall = false;
			//DOES THIS BUT WE HAVE TO MAKE SURE TARGET IS IN THE RANGE
			//Player is using action2 on target number 511
			
		}
		else if (ig.input.released('click') && this.inFocus() && this.ready && !ig.game.actionCall) {
			//ig.game.flashMUstats(this.muName, this.cardData);
			if (this.selected && !this.hasMoved && this.myCharacter){
				this.selectMe();
			}
			else if (this.selected){
				this.unselectMe();
			}
			else if (!this.selected){
				this.selectMe();
			}
			
		}
		
		
		//Select me 2
		if (ig.game.pieceSelected == this.name && ig.game.tileNumberSelected == this.myTile){
			this.selected = true;
		}
		
		this.parent();
	},
	animateMe: function(){		
		if (this.characterNum == 1){
			if (this.killMeNow){
				this.currentAnim = this.player == 1 ? this.anims.p1c1chillKill : this.anims.p2c1chillKill;
			}
			else if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.player == 1 ? this.anims.p1c1selectMe : this.anims.p2c1selectMe;
			}
			else{
				this.currentAnim = this.player == 1 ? this.anims.p1c1chill : this.anims.p2c1chill;
			}
		}
		else if (this.characterNum == 2){
			if (this.killMeNow){
				this.currentAnim = this.player == 1 ? this.anims.p1c2chillKill : this.anims.p2c2chillKill;
			}
			else if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.player == 1 ? this.anims.p1c2selectMe : this.anims.p2c2selectMe;
			}
			else{
				this.currentAnim = this.player == 1 ? this.anims.p1c2chill : this.anims.p2c2chill;
			}
		}
		else if (this.characterNum == 3){
			if (this.killMeNow){
				this.currentAnim = this.player == 1 ? this.anims.p1c3chillKill : this.anims.p2c3chillKill;
			}
			else if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.player == 1 ? this.anims.p1c3selectMe : this.anims.p2c3selectMe;
			}
			else{
				this.currentAnim = this.player == 1 ? this.anims.p1c3chill : this.anims.p2c3chill;
			}
		}
		else{
			console.log('characterNum not set = ' + this.characterNum)
		}
	},
	killMe: function(){
		if (!this.killMeNow){
			this.anims.p1c1chillKill.rewind();
			this.anims.p1c2chillKill.rewind();
			this.anims.p1c3chillKill.rewind();
			this.anims.p2c1chillKill.rewind();
			this.anims.p2c2chillKill.rewind();
			this.anims.p2c3chillKill.rewind();
			this.killMeNow = true;
			this.killMeTimer.set(2);
		}
	},
	kill: function(from){
		if (parseInt(ig.game[this.mydboVar].health) <= 0 || ig.game[this.mydboVar].location == 86 || ig.game[this.mydboVar].health <= 0){
			checkForGameEnd();
			this.parent();
		}
		else{
			this.parent();
		}
	},
	inFocus: function() {
    return (
       (this.pos.x <= (ig.input.mouse.x + ig.game.screen.x)) &&
       ((ig.input.mouse.x + ig.game.screen.x) <= this.pos.x + this.size.x) &&
       (this.pos.y <= (ig.input.mouse.y + ig.game.screen.y)) &&
       ((ig.input.mouse.y + ig.game.screen.y) <= this.pos.y + this.size.y)
    );
 	}
		
});
EntityMoveindicator=ig.Entity.extend({
	size: {x: 128, y: 128},
	maxVel: {x: 000, y: 000},
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	zIndex: -5,
	
	selected: false,
	displayAs: false,
	myCharacterName: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 0.1)',
	animSheet: new ig.AnimationSheet( 'media/square.png', 128, 128 ),
	//clickSound: new ig.Sound( 'media/sounds/new-game.*' ),
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);	
		this.addAnim( 'notselected', 1, [0], true );
		this.addAnim( 'selected', 1, [1], true );
		this.addAnim( 'canMoveCanActNotSelected', 1, [33], true );
		this.addAnim( 'canMoveCanActSelected', 1, [37], true );
		this.addAnim( 'canMoveNotSelected', 1, [32], true );
		this.addAnim( 'canMoveSelected', 1, [36], true );
		this.addAnim( 'canActNotSelected', 1, [34], true );
		this.addAnim( 'canActSelected', 1, [38], true );
		this.addAnim( 'myPieceSelected', .15, [1,22,23,24,25,26,27,28,29,30,31,30,29,28,27,26,25,24,23,22,1,1,1]);

		ig.game.sortEntitiesDeferred();
		
	},
	reset: function( x, y, settings ) {
		ig.game.sortEntitiesDeferred();
		this.parent( x, y, settings );
	 },

	
	update: function() {
		
		if (ig.game.getEntityByName(this.myCharacterName)){
			//My Character Object
			var myChar = ig.game.getEntityByName(this.myCharacterName);
			var tile = ig.game.getEntityByName(tileName);
			
			//Animate Me
			if (!ig.game.turnReporting && ig.game.gameActive && !ig.game.playerWon){
				if (myChar.hasMoved && myChar.hasActed){
					//this.currentAnim = ig.game.pieceSelected == this.myCharacterName ? this.anims.selected : this.anims.notselected; 
					this.currentAnim = this.anims.notselected;
				}
				else if (!myChar.hasMoved && myChar.hasActed){
					this.currentAnim = ig.game.pieceSelected == this.myCharacterName ? this.anims.canMoveSelected : this.anims.canMoveNotSelected; 
				}
				else if (myChar.hasMoved && !myChar.hasActed){
					this.currentAnim = ig.game.pieceSelected == this.myCharacterName ? this.anims.canActSelected : this.anims.canActNotSelected; 
				}
				else if (!myChar.hasMoved && !myChar.hasActed){
					this.currentAnim = ig.game.pieceSelected == this.myCharacterName ? this.anims.canMoveCanActSelected : this.anims.canMoveCanActNotSelected; 
				}
			}
			else if (ig.game.turnReporting || !ig.game.playerWon){
				this.currentAnim = this.anims.notselected;
			}
			if (myChar.myTile && myChar.myTile != 0 && myChar.myTile != 86){
				var tileName = "tn" + myChar.myTile;
				var tile = ig.game.getEntityByName(tileName);
				this.pos.x = tile.pos.x;
				this.pos.y = tile.pos.y;
			}
			else if (myChar.myTile && myChar.myTile != 86){
				this.kill();
			}
		}
		else{
			this.kill();
		}
		this.parent();
	},
	
	kill: function(){
		this.parent();
	}
});
	ig.EntityPool.enableFor( EntityCharacterpiece );
	ig.EntityPool.enableFor( EntityMoveindicator );
});