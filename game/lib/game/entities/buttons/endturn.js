ig.module(
	'game.entities.buttons.endturn'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityEndturn=ig.Entity.extend({
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
		//console.log(this.name + " spawned");
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
		if (ig.game.gameOver){
			this.kill();
		}
		
		if (ig.game.turnEnded || ig.game.transitioning || ig.game.turnReporting){
			this.ready = false;
		}
		else{
			this.ready = true;
		}
		
		if (this.inFocus() && !ig.game.openButtonMenu && this.ready){
			ig.game.etButHover = true;
		}
		else if (ig.game.etButHover){
			ig.game.etButHover = false;
		}
		if (ig.input.released('click') && this.inFocus() && this.ready && !ig.game.openButtonMenu) {
			if (this.checkForPlayedCharacters()){
				endTurn(1);
			}
		}

		this.parent();
	},
	checkForPlayedCharacters: function(){
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
				
		var c1Location = ig.game.playerNumber == "p1" ? ig.game.p1C1data.location : ig.game.p2C1data.location;
		var c2Location = ig.game.playerNumber == "p1" ? ig.game.p1C2data.location : ig.game.p2C2data.location;
		var c3Location = ig.game.playerNumber == "p1" ? ig.game.p1C3data.location : ig.game.p2C3data.location;
		
		if (c1Location == 0 && c2Location == 0 && c3Location == 0 && ig.game.roundNumber == 1){
			ig.game.spawnConfirmBox("You haven't played a character yet. If you don't play one, the CPU will randomly choose one of your characters to enter the arena. Are you sure that you want to end your turn?", 3, 7)
			return false;
		}
		else{
			return true;
		}
		
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.etButWidth;
		this.size.y = ig.game.etButHeight; 
		
		this.pos.x = ig.game.etButX + ig.game.screen.x;
		this.pos.y = ig.game.etButY + ig.game.screen.y;
	},
	kill: function(){
		console.log('end turn button is ded');
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
ig.EntityPool.enableFor( EntityEndturn );
});