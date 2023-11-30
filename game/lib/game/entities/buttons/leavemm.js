ig.module(
	'game.entities.buttons.leavemm'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityLeavemm=ig.Entity.extend({
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
		if (!ig.game.menuScreen || ig.game.menuScreenNum != 2){
			this.kill();
		}
		if (this.inFocus()){
			ig.game.lmmHover = true;
		}
		else if (ig.game.lmmHover){
			ig.game.lmmHover = false;
		}
		if (ig.input.released('click') && this.inFocus() && lookingForMatch && ig.game.menuScreenNum == 2) {
			if (!ig.game.muteGame){	
				this.clickSound.volume = .25; 
				this.clickSound.play();
			}
			tryToLeaveWaitingRoom();
		}
		this.parent();
		
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.lmmButWidth;
		this.size.y = ig.game.lmmButHeight; 
		
		this.pos.x = ig.game.lmmButX + ig.game.screen.x;
		this.pos.y = ig.game.lmmButY + ig.game.screen.y;
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
ig.EntityPool.enableFor( EntityLeavemm );
});