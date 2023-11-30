ig.module(
	'game.entities.buttons.deck'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityDeck=ig.Entity.extend({
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
		if (ig.input.released('click') && this.inFocus() && ig.game.menuScreenNum == 1) {
			if (!ig.game.muteGame){	
				this.clickSound.volume = .25; 
				this.clickSound.play();
			}
			
			if (this.num == 1){
				updateDeckInSession(mySavedDeck1Flat);
				selectedDeck = 1;
			}
			else if (this.num == 2){
				updateDeckInSession(mySavedDeck2Flat);
				selectedDeck = 2;
			}
			else if (this.num == 3){
				updateDeckInSession(mySavedDeck3Flat);
				selectedDeck = 3;
			}
			else if (this.num == 4){		
				var cTxt = `Would you like to open the DECK BUILDER program?`;
				setConfirmation(cTxt, 5, 6);
			}
		}
		this.parent();
	},
	setSizeAndLoc: function(){

		this.size.x = ig.game.deckButtonWidth;
		this.size.y = ig.game.deckButtonHeight; 
		
		if (this.num == 1){
			this.pos.x = ig.game.deckButton1X + ig.game.screen.x;
		}
		else if (this.num == 2){
			this.pos.x = ig.game.deckButton2X + ig.game.screen.x;
		}
		else if (this.num == 3){
			this.pos.x = ig.game.deckButton3X + ig.game.screen.x;
		}
		this.pos.y = ig.game.deckButtonY + ig.game.screen.y;
		
		//Deck builder
		if (this.num == 4){		
			this.pos.x = ig.game.deckButton1X + ig.game.screen.x;
			this.pos.y = ig.game.deckButtonY + ig.game.deckButtonHeight * 1.15 + ig.game.screen.y;
			this.size.x = ig.game.deckButtonWidth * 3.66;
			this.size.y = ig.game.deckButtonHeight * .66;
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
ig.EntityPool.enableFor( EntityDeck );
});