ig.module(
	'game.entities.statuseffectnumbers'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityStatuseffectnumbers=ig.Entity.extend({
	size: {x: 128, y: 128},
	offset: {x: 0, y: 0},
	maxVel: {x: 1000, y: 1000},
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	goodOrBad: false,
	myPiecePosX: false,
	myPiecePosY: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 0.1)',
	//openSound: new ig.Sound( 'media/sounds/open-01.*' ),
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);	
		this.killMeTimer = new ig.Timer(4);
		ig.game.statusNumberNames.push(this.name);
	},
	reset: function( x, y, settings ) {
		this.parent( x, y, settings );
		this.killMeTimer.set(4);
		ig.game.statusNumberNames.push(this.name);
    },



	deselectPlayerOccupiedTiles: function() {
		//Get all adjacent tiles
		var adjacentTiles = ig.game.getAdjacentTiles(this.myTile);

		//Get adjacent tiles that are occupied by player
		var playerOccupiedTiles = ig.game.getAdjacentPlayerLocations(this.player, this.myTile);
		
		//Kill me
		if (this.killMeTimer.delta() > 0){
			this.kill();
		}
	},

	update: function() {
		this.vel.x = 0;
		this.vel.y = -50;
		
		if (ig.game.getEntityByName(`ch${this.characterID}`)){
			var charAbove = ig.game.getEntityByName(`ch${this.characterID}`);
			this.pos.x = charAbove.pos.x;
		}
		else if (this.myPiecePosX){
			this.pos.x = this.myPiecePosX;
		}
				
		if (this.killMeTimer.delta() > 0){
			this.kill();
		}
		
		this.parent();
	},
	removeStatusNumberAfterDeath: function(){
		// Find the index of the dboVar in the array
		const index = ig.game.statusNumberNames.indexOf(this.name);

		if (index > -1) {
			ig.game.statusNumberNames.splice(index, 1);
		}
	},
	kill: function(){
		this.removeStatusNumberAfterDeath();
		this.parent();
	}
		
});
	ig.EntityPool.enableFor( EntityStatuseffectnumbers );
});