ig.module(
	'game.entities.buttons.confirm'
)
.requires(
	'impact.entity',
	'impact.entity-pool'
)
.defines(function(){
	
EntityConfirm=ig.Entity.extend({
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
	
	nClickSound: new ig.Sound( 'media/sounds/confirm-n.*' ),
	yClickSound: new ig.Sound( 'media/sounds/confirm-y.*' ),
	
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
		if (!ig.game.confirmBox){
			this.kill();
		}

		if (ig.input.released('click') && this.inFocus()) {
			//Close Confirmation Box
			ig.game.confirmBox = false;
			
			if (this.name == "cYes"){
				if (!ig.game.muteGame){	
					this.yClickSound.volume = .2; 
					this.yClickSound.play();
				}
				//Start Game Button 
				if (this.num == 1){ //Install Wallet
					openNewTab("https://metamask.io/download/");
				}
				else if (this.num == 2){ //Connect Wallet
					connectMyWallet();
				}
				else if (this.num == 3){ //Get Signature
					getSignature('game start button');
				}
				else if (this.num == 4){ //Switch Network to Preferred
					switchNetwork(preferredNetworkSwitchCode);
					console.log('switch with ' + preferredNetworkSwitchCode)
				}
				else if (this.num == 6){ //Go to deck builder
					openSameTab("/?buildDeck=true&skipPrompt=true")
				}
				else if (this.num == 7){ //End turn
					endTurn();
				}
				else if (this.num == 8){ //Get TestNet AVAX
					openNewTab("https://faucets.chain.link/fuji");
				}
				else{
					alert('yes ' + this.num);
				}
			}
			else if (this.name == "cNo"){
				if (!ig.game.muteGame){	
					this.nClickSound.volume = .2; 
					this.nClickSound.play();
				}
				//Start Menu * No Wallet **
				if (this.num == 1){
					var msg = `You need a browser wallet to play this game.`;
					ig.game.spawnAlertBox(msg, 5, 1); //txt, txtSize, num
				}
				else if (this.num == 3){ //Get Signature
					var msg = `We need to verify that you control this wallet before you can play.`;
					ig.game.spawnAlertBox(msg, 5, 3); //txt, txtSize, num
				}
				else if (this.num == 4){ //Get Signature
					var msg = `You must switch to the ${preferredNetwork1} to play this game.`;
					ig.game.spawnAlertBox(msg, 5, 4); //txt, txtSize, num
				}
				else if (this.num == 8){ //Get Signature
					var msg = `Ok that's fine but you won't be able to mint RedCoin then.`;
					ig.game.spawnAlertBox(msg, 5, 8); //txt, txtSize, num
				}
				//Do Nothing if Else
			}
			
		}
		this.parent();
	},
	
	setSizeAndLoc: function(){
		this.size.x =  ig.game.confirmButWidth;
		this.size.y =  ig.game.confirmButHeight; 
		
		
		if (this.name == "cYes"){
			this.pos.x =ig.game.yConfirmButX + ig.game.screen.x;
		}
		else if (this.name == "cNo"){
			this.pos.x =ig.game.nConfirmButX + ig.game.screen.x;
		}
		this.pos.y = ig.game.confirmButY + ig.game.screen.y;
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
ig.EntityPool.enableFor( EntityConfirm );
});