ig.module(
	'game.entities.buttons.charactercard'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityCharactercard=ig.Entity.extend({
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
		

		if (ig.game.turnEnded || ig.game.characterDeployed || ig.game.turnReporting || ig.game.openButtonMenu){
			this.ready = false;
		}
		else{
			this.ready = true;
		}
		
		if (this.inFocus() && this.ready || ig.game.openButtonMenuDisplay == "characterCards" && ig.game.openButtonMenu && this.ready){
			ig.game.ccButHover = true;
		}
		else if (ig.game.ccButHover){
			ig.game.ccButHover = false;
		}
		if (ig.input.released('click') && this.inFocus() && this.ready) {
			var clearSpace = ig.game.getReadyToPlayCharacter();
			if (clearSpace){
				var c1Location = ig.game.playerNumber == "p1" ? ig.game.p1C1data.location : ig.game.p2C1data.location;
				var c2Location = ig.game.playerNumber == "p1" ? ig.game.p1C2data.location : ig.game.p2C2data.location;
				var c3Location = ig.game.playerNumber == "p1" ? ig.game.p1C3data.location : ig.game.p2C3data.location;
		
				if (c1Location > 0 && c2Location > 0 && c3Location > 0 ){
					var msg = `You have deployed all your characters already.`;
					ig.game.spawnAlertBox(msg, 5, 99); //txt, txtSize, num
				}
				else{
					if (!ig.game.muteGame){	
						this.clickSound.volume = .25; 
						this.clickSound.play();
					}
					ig.game.openButtonMenu = true;
					ig.game.openButtonMenuDisplay = "characterCards";
					ig.game.spawnEntity( EntityCloseombut, 0, 0);
				
					//Spawn buttons
					if (!ig.game.character1played){
						ig.game.spawnEntity( EntityPlaycharacter, 0, 0, {num: 1});
					}
					if (!ig.game.character2played){
						ig.game.spawnEntity( EntityPlaycharacter, 0, 0, {num: 2});
					}
					if (!ig.game.character3played){
						ig.game.spawnEntity( EntityPlaycharacter, 0, 0, {num: 3});
					}
				}
			}
		}
		this.parent();
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.ccButWidth;
		this.size.y = ig.game.ccButHeight; 
		
		this.pos.x = ig.game.ccButX + ig.game.screen.x;
		this.pos.y = ig.game.ccButY + ig.game.screen.y;
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
ig.EntityPool.enableFor( EntityCharactercard );
});