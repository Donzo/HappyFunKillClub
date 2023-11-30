ig.module(
	'game.entities.buttons.playcharacter'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityPlaycharacter=ig.Entity.extend({
	size: {x: 1, y: 1},
	maxVel: {x: 000, y: 000},
	name: null,
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	clicked: false,
	num: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 1)',
	
	clickSound: new ig.Sound( 'media/sounds/ok-03.*' ),
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);	
		this.giveMeASecond = new ig.Timer(.33);
	},
	reset: function( x, y, settings ) {
		this.parent( x, y, settings );
		this.giveMeASecond.set(.33);
		this.clicked = false;
		//console.log(this.name + " spawned");
    },
	
	update: function() {
		
		this.setSizeAndLoc();
		
		//Kill Conditions
		if (!ig.game.openButtonMenu || ig.game.openButtonMenuDisplay != "characterCards"){
			this.kill();
		}
		if (this.inFocus()){
			if (this.num == 1){
				ig.game.pcBut1Hover = true;
			}
			else if (this.num == 2){
				ig.game.pcBut2Hover = true;
			}
			else if (this.num == 3){
				ig.game.pcBut3Hover = true;
			}
		}
		else if (this.num == 1 && ig.game.pcBut1Hover){
			ig.game.pcBut1Hover = false;
		}
		else if (this.num == 2 && ig.game.pcBut2Hover){
			ig.game.pcBut2Hover = false;
		}
		else if (this.num == 3 && ig.game.pcBut3Hover){
			ig.game.pcBut3Hover = false;
		}
		if (ig.input.released('click') && this.inFocus()) {
			var pNum = ig.game.playerNumber == "p1" ? 1: 2;
			var deployTile = pNum == 1 ? 4 : 8;
			
			
			var tileName = "tn"+ deployTile;
			var tile = ig.game.getEntityByName(tileName);
			var txt = "Your deployment tile is occupied. You can not play another character until your deployment tile is empty."
			if (tile.iAmOccupied){
				console.log(`pNum = ${pNum} && deployTile = ${deployTile} and ig.game.playerNumber = ${ig.game.playerNumber}`)
				ig.game.spawnAlertBox(txt, 5, 777)
			}
			else{
				if (!ig.game.muteGame){	
					this.clickSound.volume = .25; 
					this.clickSound.play();
				}
				this.playCharacter();
			}
		}
		this.parent();
	},
	playCharacter: function(){
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var deployTile = pNum == 1 ? 4 : 8;
		
		if (this.num == 1){
			ig.game.pcBut1Hover = true;
		}
		else if (this.num == 2){
			ig.game.pcBut2Hover = true;
		}
		else if (this.num == 3){
			ig.game.pcBut3Hover = true;
		}
		
		var tileName = "tn"+ deployTile;
		var tile = ig.game.getEntityByName(tileName);
		ig.game.tileNumberSelected = deployTile;
		ig.game.playDeployCharacterSound();
		ig.game.characterDeployed = true;
		//Make a note that this character was played.
		var whichCharacterPlayed = `character${this.num}played`;
		ig.game[whichCharacterPlayed] = true;
		ig.game.spawnEntity( EntityCharacterpiece, tile.pos.x, tile.pos.y, { player: pNum, characterNum: this.num, myTile: deployTile});
		
		ig.game.tryingToPlayCharacterNumber = this.num;
		ig.game.openButtonMenu = false;
		ig.game.openButtonMenuDisplay = false;
		
		//Set Location for Character (just deployed)
		ig.game[`${ig.game.playerNumber}C${this.num}data`].location = deployTile;
		//Initialize Action/Target (set to false)
		ig.game[`${ig.game.playerNumber}C${this.num}data`].action = false;
		ig.game[`${ig.game.playerNumber}C${this.num}data`].target = false;
		
		if (ig.game.roundNumber == 1){
			setTimeout(function() {
				if (!ig.game.openButtonMenu && !ig.game.displayCard){
	    			//ig.game.spawnConfirmBox("There are no more moves for you to make this round. End your turn?", 4, 7);
	    		}
			},2222);
		}
	},
	setSizeAndLoc: function(){
		
		this.pos.x = ig.game.OBMcrdX + ig.game.screen.x;
		this.size.x = ig.game.OBMcrdWidth;
		
		if (this.num == 1){
			this.pos.y = ig.game.OBMcard1Y + ig.game.screen.y - ig.game.adjustPCButForLineHeight;
			this.size.y = ig.game.OBMcrd1Height;
		}
		else if (this.num == 2){
			this.pos.y = ig.game.OBMcard2Y + ig.game.screen.y - ig.game.adjustPCButForLineHeight;
			this.size.y = ig.game.OBMcrd2Height;
		}
		else if (this.num == 3){
			this.pos.y = ig.game.OBMcard3Y + ig.game.screen.y - ig.game.adjustPCButForLineHeight;
			this.size.y = ig.game.OBMcrd3Height;
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
ig.EntityPool.enableFor( EntityPlaycharacter );
});