ig.module(
	'game.entities.moveattempt'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityMoveattempt=ig.Entity.extend({
	size: {x: 128, y: 128},
	offset: {x: 0, y: 64},
	maxVel: {x: 000, y: 000},
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	
	myCharacter: null,
	myTile: 0,
	myTileName: null,
	playerNum: false,
	pieceName: false,
	characterNum: false,
	dboVar: false,
	trMA: false,
	
	//Array
	cardData: null,
	
	selected: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 0.1)',
	animSheet: new ig.AnimationSheet( 'media/character-pieces.png', 128, 192 ),
	//openSound: new ig.Sound( 'media/sounds/open-01.*' ),
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);	

		this.addAnim( 'p1c1chill', 1, [4], true );
		this.addAnim( 'p2c1chill', 1, [6], true );
		this.addAnim( 'p1c1selectMe', 1, [5], true );
		this.addAnim( 'p2c1selectMe', 1, [7], true );
		this.addAnim( 'p1c2chill', 1, [12], true );
		this.addAnim( 'p2c2chill', 1, [14], true );
		this.addAnim( 'p1c2selectMe', 1, [13], true );
		this.addAnim( 'p2c2selectMe', 1, [15], true );
		this.addAnim( 'p1c3chill', 1, [20], true );
		this.addAnim( 'p2c3chill', 1, [22], true );
		this.addAnim( 'p1c3selectMe', 1, [21], true );
		this.addAnim( 'p2c3selectMe', 1, [23], true );
		
		
		this.nameMe();
		this.setLocation();
	},
	reset: function( x, y, settings ) {
		this.parent( x, y, settings );
		this.selected = false;
		this.nameMe();
		this.setLocation();
    },
    setLocation: function(){
    	
    	ig.game.clearTileColors();
		
		//Set parent location to here this tile.
		if (!this.trMA){
			ig.game[this.parentDBO].location = this.myTile;
		}

    },
	nameMe: function(){
		this.name =  `${ig.game.playerNumber}C${ig.game.sPieceCharacterNum}MoveAttempt`;
		this.parentDBO =  `${ig.game.playerNumber}C${ig.game.sPieceCharacterNum}data`;
	},

	update: function() {
		
		if (ig.game.turnReporting && !this.trMA){
			//Reset my tile properties
			var tileName = "tn"+ this.myTile;
			var tile = ig.game.getEntityByName(tileName);
			tile.attemptedMoveHere = false;
			this.kill();
		}
		else if (this.trMA && !ig.game.turnReporting || ig.game.thePieceWasSpawned && this.trMA){
			var tileName = "tn"+ this.myTile;
			var tile = ig.game.getEntityByName(tileName);
			tile.attemptedMoveHere = false;
			this.kill();
		}
		
		if (ig.game.openButtonMenu || ig.game.transitioning || ig.game.turnEnded || ig.game.actionCall){
			this.ready = false;
		}
		else{
			this.ready = true;
		}
		this.animateMe();
		

		//Click me
		if (ig.input.released('click') && this.inFocus() && this.ready) {
			 
			//Currently does nothing...
			if (!this.selected){
				
			}
			else if (this.selected){
			
			}

		}
		
		
		
		/*
		//Unselect me
		if (ig.game.pieceSelected == this.name && ig.game.tileNumberSelected == this.myTile){
			this.selected = true;
		}
		else{
			this.selected = false;
		}
		*/
		
		this.parent();
	},
	animateMe: function(){	
			
		if (this.characterNum == 1){
			if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c1selectMe : this.anims.p2c1selectMe;
			}
			else{
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c1chill : this.anims.p2c1chill;
			}
		}
		else if (this.characterNum == 2){
			if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c2selectMe : this.anims.p2c2selectMe;
			}
			else{
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c2chill : this.anims.p2c2chill;
			}
		}
		else if (this.characterNum == 3){
			if (this.selected && !ig.game.lookingAtMoves){
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c3selectMe : this.anims.p2c3selectMe;
			}
			else{
				this.currentAnim = this.playerNum == 1 ? this.anims.p1c3chill : this.anims.p2c3chill;
			}
		}
	},
	kill: function(){
		this.parent();
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
ig.EntityPool.enableFor( EntityMoveattempt );
});