ig.module(
	'game.entities.buttons.actioncall'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityActioncall=ig.Entity.extend({
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
		if (!ig.game.gameActive || !ig.game.displayCard || ig.game.displayCardView != 2){
			this.kill();
		}
		if (this.inFocus()){
			if (this.num == 1){
				ig.game.act1Hover = true;
			}
			else if (this.num == 2){
				ig.game.act2Hover = true;
			}
			else if (this.num == 3){
				ig.game.act3Hover = true;
			}
		}
		else{
			if (this.num == 1){
				ig.game.act1Hover = false;
			}
			else if (this.num == 2){
				ig.game.act2Hover = false;
			}
			else if (this.num == 3){
				ig.game.act3Hover = false;
			}
		}
		//Cans I afford?
		if (ig.game.canAffordAction1 && this.num == 1){
			this.canAfford = true;
		}
		else if (ig.game.canAffordAction2 && this.num == 2){
			this.canAfford = true;
		}
		else if (ig.game.canAffordAction3 && this.num == 3){
			this.canAfford = true;
		}
		else{
			this.canAfford = false;
		}
		
		if (ig.input.released('click') && this.inFocus() && this.canAfford) {
			var pNum = ig.game.playerNumber == "p1" ? 1: 2;
			var eNum = ig.game.playerNumber == "p2" ? 1: 2;
			var deployTile = pNum == 1 ? 4 : 8;
			
			
			ig.game.clearTileColors(); //Clear tile colors first...
			ig.game.actionCall = true;
			//Determine which action to take based on the button number
			const actionNum = `action${this.num}`;
			const actionName = ig.game[actionNum];
			const actionType = ig.game[`${actionNum}type`];
			const actionCost = ig.game[`${actionNum}cost`];
			const hasActed = ig.game[`hasActed${this.num}`];
			
			ig.game.actionNum = actionNum;
			ig.game.actionName = actionName;
			ig.game.actionType = actionType;
			ig.game.actionCost = actionCost;
			
			if (!hasActed) {

				//Function to handle different action types
				const handleActionType = (type) => {
					switch (type) {
						case "Melee":
							// Do Melee things
							ig.game.highlightAdjacentTiles(ig.game.charCurLoc, "meleeRange");
							ig.game.deselectPlayerOccupiedTiles(ig.game.charCurLoc, pNum);
							ig.game.highlightAdjacentEnemyTiles(pNum, ig.game.charCurLoc, "meleeTarget");
							ig.game.clearOneTileColor(deployTile);
							ig.game.clearOneTileColor(ig.game.charCurLoc);
							
							break;
						case "Ranged":
							// Do Ranged things
							ig.game.highlightAllTiles("rangeRange");
							ig.game.highlightCharTiles(eNum, "rangeTarget");
							ig.game.clearOneTileColor(deployTile);
							ig.game.deselectPlayerOccupiedTiles(ig.game.charCurLoc, pNum);
							break;
						case "Boost":
							// Do Boost things
							ig.game.colorTile(ig.game.charCurLoc, "boost");
							break;
						case "Support":
							// Do Support things
							ig.game.highlightAdjacentTiles(ig.game.charCurLoc, "supportMeleeRange");
							ig.game.highlightAdjacentEnemyTiles(eNum, ig.game.charCurLoc, "boost"); //Selecting ENUM so this will target player characters.
							ig.game.highlightAdjacentEnemyTiles(pNum, ig.game.charCurLoc, "invalidMove");
							break;
						case "R Support":
							// Do Ranged Support things
							ig.game.highlightAllTiles("supportRangeRange");
							ig.game.highlightCharTiles(pNum, "supportRangeTarget");
							break;
						case "Drain":
							// Do Ranged Support things
							ig.game.highlightAllTiles("drainMeleeRange");
							ig.game.highlightAdjacentEnemyTiles(eNum, ig.game.charCurLoc, "drainMeleeTarget");
							ig.game.deselectPlayerOccupiedTiles(ig.game.charCurLoc, pNum);
							ig.game.clearOneTileColor(deployTile);
							break;
						case "R Drain":
							// Do Ranged Support things
							ig.game.highlightAllTiles("drainRangeRange");
							ig.game.highlightCharTiles(eNum, "drainRangeTarget");
							ig.game.deselectPlayerOccupiedTiles(ig.game.charCurLoc, pNum);
							ig.game.clearOneTileColor(deployTile);
							break;
						default:
							// Handle unexpected type
							console.error("Unexpected action type:", type);
							break;
					}
				};
				handleActionType(actionType);
				//Display the stats
				ig.game.displayActionStats = true;
				
			}
		}
		this.parent();
	},
	setSizeAndLoc: function(){
		
		this.pos.x = ig.game.actionPosX + ig.game.screen.x;
		this.size.x = ig.game.actionTxtWidth;
		
		if (this.num == 1){
			this.size.y = ig.game.action1Height; 
			this.pos.y = ig.game.action1posY + ig.game.screen.y;
		}
		else if (this.num == 2){
			this.size.y = ig.game.action2Height; 
			this.pos.y = ig.game.action2posY + ig.game.screen.y;
		}
		else if (this.num == 3){
			this.size.y = ig.game.action3Height; 
			this.pos.y = ig.game.action3posY + ig.game.screen.y;
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
ig.EntityPool.enableFor( EntityActioncall );
});