ig.module(
	'game.entities.square'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntitySquare=ig.Entity.extend({
	size: {x: 128, y: 128},
	maxVel: {x: 000, y: 000},
	type: ig.Entity.TYPE.NONE,
	checkAgainst: ig.Entity.TYPE.NONE,
	collides: ig.Entity.COLLIDES.NEVER,
	tileNumber: 0,
	
	selected: false,
	displayAs: false,
	
	_wmDrawBox: true,
	_wmBoxColor: 'rgba(245, 66, 212, 0.1)',
	animSheet: new ig.AnimationSheet( 'media/square.png', 128, 128 ),
	//clickSound: new ig.Sound( 'media/sounds/new-game.*' ),
	
	init: function( x, y, settings ) {
		this.parent(x, y, settings);	
		this.addAnim( 'notselected', 1, [0], true );
		this.addAnim( 'selected', 1, [1], true );
		this.addAnim( 'myPieceSelected', .15, [1,22,23,24,25,26,27,28,29,30,31,30,29,28,27,26,25,24,23,22,1,1,1]);
		this.addAnim( 'validLoc', 1, [2], true );
		this.addAnim( 'selectedLoc', 1, [3], true );
		this.addAnim( 'validMove', 1, [18], true );
		this.addAnim( 'invalidMove', 1, [19], true );
		this.addAnim( 'theirDeploymentTile', 1, [15], true );
		this.addAnim( 'myDeploymentTile', 1, [21], true );
		this.addAnim( 'canMoveCanActNotSelected', 1, [33], true );
		this.addAnim( 'canMoveCanActSelected', 1, [37], true );
		this.addAnim( 'canMoveNotSelected', 1, [32], true );
		this.addAnim( 'canMoveSelected', 1, [36], true );
		this.addAnim( 'canActNotSelected', 1, [34], true );
		this.addAnim( 'canActSelected', 1, [38], true );
		//New Animations
		this.addAnim( 'meleeRange', 1, [13], true ); // was 4
		this.addAnim( 'meleeTarget', 1, [5], true ); //was 5
		this.addAnim( 'rangeRange', 1, [6], true );
		this.addAnim( 'rangeTarget', 1, [7], true );
		this.addAnim( 'boost', 1, [8], true );
		this.addAnim( 'drainMeleeRange', 1, [9], true );
		this.addAnim( 'drainMeleeTarget', 1, [10], true );
		this.addAnim( 'drainRangeRange', 1, [11], true );
		this.addAnim( 'drainRangeTarget', 1, [12], true );
		this.addAnim( 'supportMeleeRange', 1, [13], true );
		this.addAnim( 'supportMeleeTarget', 1, [14], true );
		this.addAnim( 'supportRangeRange', 1, [16], true );
		this.addAnim( 'supportRangeTarget', 1, [17], true );
		this.addAnim( 'actor', 1, [2], true );
		this.addAnim( 'target', 1, [7], true );
		this.addAnim( 'sTarget', 1, [8], true );
		this.addAnim( 'missed', 1, [1], true );
		
	},
	reset: function( x, y, settings ) {
		this.parent( x, y, settings );
	 },
	amOccupied: function() {
		var allPieceLocations = ig.game.getAllPieceLocations();
		return allPieceLocations.includes(this.tileNumber);
	},
	moveMeHere: function(parentID, parentName, player){
		var parent = ig.game.getEntityByName(parentName);
		//player is p2 or whatever
		var playerNumber = player.substring(1);
		ig.game.spawnEntity( EntityMoveattempt, this.pos.x, this.pos.y, { trMA: false, myParentName: parentName, myParentID: parentID, playerNum: playerNumber, myTile: this.tileNumber, characterNum: parent.characterNum});	
	},
	isTileNumberAvailable: function() {
		var enemyLocations = ig.game.getPieceLocationsOfEnemy();
		return !enemyLocations.includes(this.tileNumber);
	},
	update: function() {
		
		if ( ig.game.titleScreen || ig.game.menuScreen || ig.game.openButtonMenu || ig.game.displayCard && !ig.game.lookingAtMoves || ig.game.actionCall || ig.game.turnEnded || ig.game.turnReporting || ig.game.playerWon){
			this.ready = false;
		}
		else{
			this.ready = true;
		}
		
		if (this.displayAs == "notselected" || this.displayAs == "selected" || this.displayAs == "validLoc" || this.displayAs == "selectedLoc"){
			this.overRideColors = true;
		}
		else{
			this.overRideColors = false;
		}
		
		if (ig.game.playerNumber == "p1" && this.tileNumber == 4 && this.overRideColors || ig.game.playerNumber == "p2" && this.tileNumber == 8 && this.overRideColors){
			this.currentAnim = this.anims.myDeploymentTile;
		}
		else if (ig.game.playerNumber == "p2" && this.tileNumber == 4 && this.overRideColors || ig.game.playerNumber == "p1" && this.tileNumber == 8 && this.overRideColors){
			this.currentAnim = this.anims.theirDeploymentTile;
		}
		else{
			this.animMe();
		}
		this.iAmOccupied = this.amOccupied();
		
		if (this.iAmOccupied && ig.game.displayCard){
			if (ig.game.getEntityByName(ig.game.pieceSelected)){
				var selectedPlayer = ig.game.getEntityByName(ig.game.pieceSelected);
				if (selectedPlayer.myTile == this.tileNumber && ig.game.lookingAtMyCharacter){
					this.currentAnim = this.anims.myPieceSelected;
				}
			}
			
		}
		
		//Click me to move around the screen and select tiles
		if (ig.input.released('click') && this.inFocus() && this.ready) {
			if (!this.selected){
				if (ig.game.lookingAtMoves && this.displayAs == "validLoc" && this.currentAnim != this.anims.myDeploymentTile && this.currentAnim != this.anims.theirDeploymentTile){
					var parent = ig.game.getEntityByName(ig.game.sPieceName);
					if (parent && !parent.hasMoved && this.isTileNumberAvailable()){		
						this.moveMeHere(ig.game.sPieceID, ig.game.sPieceName, ig.game.playerNumber, ig.game.sPieceCharacterNum);
						parent.hasMoved = true;
						this.attemptedMoveHere = true;
					}
				}
				else{
					if (!this.iAmOccupied && !this.attemptedMoveHere){
						ig.game.tileNumberSelected = this.tileNumber;
						ig.game.xSelectedPos = this.pos.x;
						ig.game.ySelectedPos = this.pos.y;
						ig.game.checkCameraBounds();
						this.selected = true;
						ig.game.lookingAtMoves = false;
						this.displayAs = "selected";
					}
					else{
						//console.log('clicked a non selected square. Did not select it because iAMOccupied = ' + this.iAmOccupied);
					}
				}
			}
			else if (!ig.game.actionCall){
				this.selected = false;
			}
		}


		//Unselect me
		if (ig.game.tileNumberSelected != this.tileNumber && !ig.game.actionCall && !ig.game.lookingAtMoves){
			this.selected = false;
			this.displayAs = "notselected";
		}
		
		this.parent();
	},

	kill: function(){
		this.parent();
	},
	
	animMe(){

	 	
	 	this.lastDisplayAs = this.displayAs;
	 	
		switch (this.displayAs) {
			case "validMove":
				this.currentAnim = this.anims.validMove;
				break;
			case "invalidMove":
				this.currentAnim = this.anims.invalidMove;
				break;
			case "meleeRange":
				this.currentAnim = this.anims.meleeRange;
				break;
			case "meleeTarget":
				this.currentAnim = this.anims.meleeTarget;
				break;
			case "rangeRange":
		  		this.currentAnim = this.anims.rangeRange;
		 		break;
			case "rangeTarget":
				this.currentAnim = this.anims.rangeTarget;
				break;
			case "boost":
				this.currentAnim = this.anims.boost;
				break;
			case "drainMeleeRange":
				this.currentAnim = this.anims.drainMeleeRange;
				break;
			case "drainMeleeTarget":
				this.currentAnim = this.anims.drainMeleeTarget;
				break;
			case "drainRangeRange":
				this.currentAnim = this.anims.drainRangeRange;
				break;
			case "drainRangeTarget":
				this.currentAnim = this.anims.drainRangeTarget;
				break;
			case "supportMeleeRange":
				this.currentAnim = this.anims.supportMeleeRange;
				break;
			case "supportMeleeTarget":
				this.currentAnim = this.anims.supportMeleeTarget;
				break;
			case "supportRangeRange":
				this.currentAnim = this.anims.supportRangeRange;
				break;
			case "supportRangeTarget":
				this.currentAnim = this.anims.supportRangeTarget;
				break;
			case "notselected":
				this.currentAnim = this.anims.notselected;
				break;
			case "selected":
				this.currentAnim = this.anims.selected;
				break;
			case "validLoc":
				this.currentAnim = this.anims.validLoc;
				break;
			case "selectedLoc":
				this.currentAnim = this.anims.selectedLoc;
				break;
			case "actor":
				this.currentAnim = this.anims.actor;
				break;
			case "target":
				this.currentAnim = this.anims.target;
				break;
			case "sTarget":
				this.currentAnim = this.anims.sTarget;
				break;
			case "missed":
				this.currentAnim = this.anims.missed;
				break;
			default:
				// Default to 'notselected' animation
				this.currentAnim = this.anims.notselected;
			break;
		}
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
ig.EntityPool.enableFor( EntitySquare );
});