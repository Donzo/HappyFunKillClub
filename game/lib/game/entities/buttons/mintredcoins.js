ig.module(
	'game.entities.buttons.mintredcoins'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityMintredcoins=ig.Entity.extend({
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
		if (!ig.game.menuScreen || mySavedDeck1.length == 0 && this.num == 1 || mySavedDeck2.length == 0 && this.num == 2 || mySavedDeck3.length == 0 && this.num == 3){
			this.kill();
		}
		if (this.inFocus()){
			ig.game.mintRedCoinButHover = true;	
		}
		else{
			ig.game.mintRedCoinButHover = false;	
		}
		if (ig.input.released('click') && this.inFocus() && ig.game.menuScreenNum == 1) {
			if (!ig.game.muteGame){	
				this.clickSound.volume = .25; 
				this.clickSound.play();
			}
			mintRedCoins();
		}
		this.parent();
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.mintRedCoinButWidth;
		this.size.y = ig.game.mintRedCoinButHeight; 
		this.pos.x = ig.game.mintRedCoinButX + ig.game.screen.x;
		this.pos.y = ig.game.mintRedCoinButY + ig.game.screen.y;
		
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
ig.EntityPool.enableFor( EntityMintredcoins );
});