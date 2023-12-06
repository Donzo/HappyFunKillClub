ig.module(
	'game.entities.buttons.characteraction'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityCharacteraction=ig.Entity.extend({
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
		if (!ig.game.gameActive || !ig.game.displayCard){
			this.kill();
		}
		if (this.inFocus()){
			ig.game.chActHover = true;
		}
		else if (ig.game.chActHover){
			ig.game.chActHover = false;
		}
		//Would be nice to check if the character acted before allowing them to double move... oh well. MVP.
		if (ig.input.released('click') && this.inFocus() && ig.game.displayCardView != 2 && ig.game[ig.game.displayCardDBO].hasDeployed) {
			//Calculate Energy costs:
			//ig.game[`${ig.game.selectedPieceDBOVar}`].a1_cost; 
			ig.game.displayCardView = 2;
			//Spawn Action Buttons
			ig.game.spawnEntity( EntityActioncall, 0, 0, { num: 1 });
			ig.game.spawnEntity( EntityActioncall, 0, 0, { num: 2 });
			ig.game.spawnEntity( EntityActioncall, 0, 0, { num: 3 });
		}
		else if (ig.input.released('click') && this.inFocus() && ig.game.displayCardView != 2 && !ig.game[ig.game.displayCardDBO].hasDeployed){
			var msg = `Characters cannot act on the round in which they are deployed.`;
			ig.game.spawnAlertBox(msg, 5, 1); //txt, txtSize, num
		}
		
		
		
		this.parent();
	},
	killMeIfCharacterActed(){
		var dboVar = ig.game.selectedPieceDBOVar;
		var actorID = `ch${ig.game[dboVar].character_id}`;
		var actorEnt = ig.game.getEntityByName(actorID);
		if (actorEnt.hasActed){
			console.log('This character has already taken an action. Kill the action button.');
			this.kill();
		}
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.chActButWidth;
		this.size.y = ig.game.chActButHeight; 
		
		this.pos.x = ig.game.chActButX + ig.game.screen.x;
		this.pos.y = ig.game.chActButY + ig.game.screen.y;
				
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
ig.EntityPool.enableFor( EntityCharacteraction );
});