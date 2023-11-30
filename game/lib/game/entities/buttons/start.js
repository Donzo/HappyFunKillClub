ig.module(
	'game.entities.buttons.start'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityStart=ig.Entity.extend({
	size: {x: 1, y: 1},
	maxVel: {x: 000, y: 000},
	name: null,
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	clicked: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 1)',
	
	//clickSound: new ig.Sound( 'media/sounds/new-game.*' ),
	
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
		
		this.size.x =  ig.game.tsButtonWidth;
		this.size.y =  ig.game.tsButtonHeight; 
		this.pos.x =ig.game.ngbX + ig.game.screen.x;
		this.pos.y = ig.game.ngbY + ig.game.screen.y;
		
		if (!ig.game.titleScreen){
			this.kill();
		}

		if (ig.input.released('click') && this.inFocus()) {
			startGame();
		}
		this.parent();
	},
	kill: function(){
		//console.log(this.name + " killed");
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
ig.EntityPool.enableFor( EntityStart );
});