ig.module( 
	'game.main' 
)
.requires(
	'impact.game',
	//'impact.debug.debug',
	'impact.font',
	'plugins.dynamic-fonts',
	'plugins.touch-button',
	'game.entities.buttons.actioncall',
	'game.entities.buttons.characteraction',
	'game.entities.buttons.charactercard',
	'game.entities.buttons.closecarddisplay',
	'game.entities.buttons.closeombut',
	'game.entities.buttons.confirm',
	'game.entities.buttons.deck',
	'game.entities.buttons.endturn',
	'game.entities.buttons.leavemm',
	'game.entities.buttons.matchmake',
	'game.entities.buttons.mintredcoins',
	'game.entities.buttons.ok',
	'game.entities.buttons.playcpu',
	'game.entities.buttons.playcharacter',
	'game.entities.buttons.start',
	'game.entities.characterpiece',
	'game.entities.moveattempt',
	'game.entities.mutebutton',
	'game.entities.statuseffectnumbers',
	'game.entities.square',
	'game.levels.l1'
)
.defines(function(){

MyGame = ig.Game.extend({
	
	//Don't forget to set this to the number of total levels or game will end early or something
	totalLevels: 2,
	
	gravity: 2000, //All entities are affected by this
	
	//Load a font
	font: new ig.Font( 'media/04b03.font.png' ),
	
	//Define Main Colors
	color1:"#FF69B4",
	color2:"#FFE9B5",
	color3:"#8B4513",
	color4:"#87CEEB",
	color5:"#DAA520",
	color6:"#D8BFD8",
	color7:"#008080",
	colorRight: "#32CD32",//Lime
	colorWrong: "#764762", //Red
	defaultStatTextColor: "#000000",

	buttonMute: new ig.Image( 'media/buttons-and-logos/button-mute.png' ),
	buttonMuted: new ig.Image( 'media/buttons-and-logos/button-muted.png' ),
	buttonMuteSmall: new ig.Image( 'media/buttons-and-logos/button-mute-small.png' ),
	buttonMutedSmall: new ig.Image( 'media/buttons-and-logos/button-muted-small.png' ),
	
	titleScreenTxt: "Welcome to the Happy Fun Kill Club!",
	
	muteGame: false,
	musicLevel: 1,
	playersWaitingNum: 0,
	moveReportingTxt1: "",
	moveReportingTxt2: "",
	moveReportingTxt3: "",
	moveReportingTxt4: "",
	moveReportingTxt5: "",
	moveReportingTxt6: "",
	
	charHealthBarsToDraw:[],
	statusNumberNames:[],
	initialOccupiedTiles:[],
	 
	//Preloaded Songs
	songs: {
		l1: new ig.Sound('media/music/schoolboy.*', false ),
		l2: new ig.Sound('media/music/spacetrip.*', false ),
		l3: new ig.Sound('media/music/wound.*', false ),
		l4: new ig.Sound('media/music/yeah.*', false ),
	},
	
	//Sounds
	closeSound: new ig.Sound('media/sounds/close-01.*'),
	deployCharacterSound: new ig.Sound('media/sounds/deploy-character.*'),
	drawSwordSound: new ig.Sound('media/sounds/draw-sword.*'),
	openSound: new ig.Sound('media/sounds/open-01.*'),
	pickSound: new ig.Sound('media/sounds/card-pick.*'),
	unpickSound: new ig.Sound('media/sounds/card-unpick.*'),
	
	missSound: new ig.Sound('media/sounds/missed.*'),
	supportHitSound: new ig.Sound('media/sounds/support.*'),
	meleeHitSound: new ig.Sound('media/sounds/melee-hit.*'),
	drainHitSound: new ig.Sound('media/sounds/drain-hit.*'),
	rangedHitSound: new ig.Sound('media/sounds/ranged-hit.*'),
	deadGuyActingSound: new ig.Sound('media/sounds/dead-guy-acting.*'),
	killCharacterSound: new ig.Sound('media/sounds/kill-character.*'),
	mintRedCoinsSound: new ig.Sound('media/sounds/mint-redcoins.*'),
	
	CCIDCounter: 0,
	MUIDCounter: 0,
	
	enemyRecoveryTime: .66,
	
	fadeColor: this.color3,
	slideColor: this.color3,
	
	titleScreen: true,
	transition: false,
	transitionType: null,
	flashScreen: false,
	flashScreenColor: null,
	flashMsgOnTime: .85,
	flashMsgOffTime: .15,
	flashMsg: true,
	
	turnEndScreenStatusTxt: "Waiting for Other Player...",
	
	//Dfont Variables
	maxHeaderHeightRatio: 0.15,
	maxHeaderHeight: null,
	maxHeaderLines: 2,
	maxHeaderLinesPortrait: 3,
	
	
	//Ending Variables
	flickerColor: false,
	flickerCount: 0,
	flickerTotalCount: 0,
	flickerFreq: 1,
	maxFlickers: 50,
	xSelected: 0,
	ySelected: 0,
	xCamera: 0,
	yCamera:0,
	xSelectedPos: 0,
	ySelectedPos: 0,
	cameraBoundX: 128,
	cameraBoundY: 96,
	
	//Health Bar Arrays
	healthBarPosX:{},
	healthBarWidth:{},
	healthBarHeight:{},
	healthBarPosY:{},
	
	menuScreen: false,
	menuScreenNum: 1,

	drawPlayCardDisplay: false,
	playingCardStep: 0,
	
	hasMoved1: false,
	hasActed1: false,
	hasMoved2: false,
	hasActed2: false,
	hasMoved3: false,
	hasActed3: false,
	
	init: function(){
		
		//Reset gameID
		this.currentGameId = false; 
		
		//Bind Inputs
		ig.input.bind(ig.KEY.MOUSE1, 'click');
		ig.input.bind( ig.KEY.LEFT_ARROW, 'left' );
		ig.input.bind( ig.KEY.RIGHT_ARROW, 'right' );
		ig.input.bind( ig.KEY.UP_ARROW, 'jump' );
		ig.input.bind( ig.KEY.SPACE, 'action' );
		
		this.transitionTimer = new ig.Timer(0);
		this.flashScreenTimer = new ig.Timer(0);
		this.flashMessageTimer = new ig.Timer(0);
		this.questionClearTimer = new ig.Timer(0);
		this.deathScreenTimer = new ig.Timer(0);
		this.musicDelayTimer = new ig.Timer(0);
		this.timeLeftInTurn = new ig.Timer(0);
		this.turnReportingTime = new ig.Timer(0);

		//Load Title Screen images into impact
		this.loadTSImages();
		
		//Call for Dynamic Fonts
		this.dFonts = new DynamicFonts();

		//Load Level
		this.LoadLevelBro( this.gData.lvl);
		
		ig.game.spawnEntity( EntityStart, 0, 0, { name: "start" });	

		if (this.savedGame ){	
			//ig.game.spawnEntity( EntityButton, 0, 0, { name: "continue" });	
		}
		
		if( ig.ua.mobile ){
			this.amImobile = true;
		}
		else{
			this.amImobile = false;
		}
		
		//this.songs.l2 = new ig.Sound('media/music/song-02.*', false );
		//this.songs.l3 = new ig.Sound('media/music/song-04.*', false );
		
		//MUSIC
		ig.music.add (this.songs.l1, 01, ["l1"] );
		ig.music.add (this.songs.l2, 02, ["l2"] );
		ig.music.add (this.songs.l3, 03, ["l3"] );
		ig.music.add (this.songs.l4, 04, ["l4"] );
		
		ig.music.loop = true;
		ig.music.volume = this.musicLevel;	
		ig.game.loadGame(); //Basically only controls saved mutes

		this.xCamera = 0;
		this.yCamera = 240;
		
	},

	update: function(){
		//Update all entities and backgroundMaps
		this.parent();
		//Set Global Pauses
		if (this.quiz || this.transition || this.titleScreen || this.deathScreen || this.levelCleared || this.endingScreen){
			ig.game.pause = true;
		}
		else{
			ig.game.pause = false;	
		}
		
		//Flash Message
		if (ig.game.readyToEnd && this.flashMsg && this.flashMessageTimer.delta() > 0){
			this.flashMessageTimer.set(this.flashMsgOffTime);
			this.flashMsg = false;		
		}
		else if (ig.game.readyToEnd && !this.flashMsg && this.flashMessageTimer.delta() > 0){
			this.flashMessageTimer.set(this.flashMsgOnTime);
			this.flashMsg = true;
		}
		
		//Clear the Cut Screen
		if (!this.cutCleared && this.transitionReady && ig.input.released('click') && !ig.game.transition){
			ig.game.sortEntitiesDeferred();
			this.cutCleared = true;
			ig.game.slideRightOut("","",3);
		}
		//End Turn Reporting screen
		if (ig.game.turnReporting && this.turnReportingTime.delta() > 0){
			endTurnReporting();
		}
		var camSpeed = 15;
		var margin = 32;
		
		if (this.screen.x < this.xCamera + margin){
			this.screen.x +=camSpeed;
		}
		if (this.screen.x > this.xCamera - margin){
			this.screen.x -=camSpeed;
		}
		if (this.screen.y < this.yCamera + margin){
			this.screen.y +=camSpeed;
		}
		if (this.screen.y > this.yCamera - margin){
			this.screen.y -=camSpeed;
		}
		//Crude Function to end game for MVP
		if (ig.game.readyToEnd && ig.input.released('click')){
			reloadPage();
		}
		
		//End turn if player runs out of time.
		if (ig.game.timeLeftInTurn.delta() > 0 && ig.game.gameActive){
			endTurn(3);
		}
	},
	
	centerCameraOnCharacterByID: function(charID){
		var characterIdentifiers = ['p1C1data', 'p1C2data', 'p1C3data', 'p2C1data', 'p2C2data', 'p2C3data'];

		for (let i = 0; i < characterIdentifiers.length; i++) {
			let characterData = ig.game[characterIdentifiers[i]];
			if (characterData && characterData.character_id == charID) {
				var tileOfCharacter = characterData.location;
				var tileName = "tn" + tileOfCharacter;
				var tile = ig.game.getEntityByName(tileName);
				if (tile){
					this.xCamera = tile.pos.x - ig.system.width / 2;
					this.yCamera = tile.pos.y - ig.system.height / 3;
				}
				break; 
			}
		}
	},
	
	draw: function(){
		//Draw all entities and backgroundMaps
		this.parent();
		
		//Draw Buttons on Mobile
		if( this.buttonSet && !this.quiz){
			this.buttonSet.draw(); 
		}

		//Draw Mute Button and HUD
		this.drawMuteButton();
		this.drawHUD();
		
		
		//Title
		if (this.titleScreen){
			this.drawTitleScreen();	
		}
		if (this.menuScreen){
			this.drawMenuScreen();	
		}
		if (this.gameActive && !ig.game.turnEnded && !ig.game.turnReporting && !ig.game.playerWon){
			this.drawCharacterCardButton();
			this.drawEndTurnButton();
		}
		if (this.charHealthBarsToDraw.length > 0){
			this.drawCharacterHealthBars();
		}
		if (this.statusNumberNames.length > 0){
			this.drawStatusNumbers();
		}
		if (this.displayCard && !ig.game.playerWon){
			this.drawCardDisplay();
		}
		
		//Open Button Menu
		if (this.openButtonMenu && !ig.game.playerWon){
			this.drawOpenButtonMenu();
		}
		//Turn Ended Screen		
		if (ig.game.turnEnded && !ig.game.playerWon){
			this.drawTurnEndedScreen();
		}
		if (ig.game.turnReporting && !ig.game.playerWon){
			this.drawTurnReportingScreen();
			if (ig.game.turnEnded){
				ig.game.turnEnded = false;
				console.log('calling this odd reset function')
			}
		}
		//Game over
		if (ig.game.playerWon){
			this.drawGameOverScreen();
		}
		//Transition ETC.
		if (this.transition){
			this.drawTransition();
		}
		
		//Confirmation and Alert Boxes		
		if (ig.game.confirmBox && !ig.game.playerWon){
			this.drawConfirmationBox(this.confirmMsg, this.confirmSize);
		}
		if (ig.game.alertBox && !ig.game.playerWon){
			this.drawAlertBox(this.alertMsg, this.alertSize);
		}
		
		//Flash Screen for various purposes
		this.flashScreenCheck();
		
	},
	LoadLevelBro: function(currentLvlNum){
		if (currentLvlNum <= this.totalLevels){
			ig.game.pause = true;
			//Get level string
			var whichLvl =  parseInt(currentLvlNum);
			//Turn string into object reference
			var lvlStr = eval("LevelL" + whichLvl);
			//Load the level
			ig.game.muteButtonAlive = false;
			this.loadLevel( lvlStr );
			this.readyToLoad = false;
			this.spawnButtons();
		}
		else{
			ig.game.gData.lvl = 1;
			ig.game.muteButtonAlive = false;
			this.LoadLevelBro(1);	
			this.readyToLoad = false;
			this.spawnButtons();
		}
	},
	spawnButtons: function(){
		//Spawn mute button if not in worldmaker
		if( !ig.game.muteButtonAlive ){ 
			ig.game.spawnEntity( EntityMutebutton, 0, 0);	
		}
	},

	checkCameraBounds: function(){
		var cameraLeftBound =  this.xCamera - this.cameraBoundX;
		var cameraRightBound = this.xCamera + this.cameraBoundX;
		var cameraTopBound = this.yCamera - this.cameraBoundY;
		var cameraBottomBound = this.yCamera + this.cameraBoundY;
		var targetX = this.xSelectedPos - ig.system.width / 2;
		var targetY = this.ySelectedPos - ig.system.height / 2;

		if (targetX < cameraLeftBound || targetX > cameraRightBound ){	
			this.xCamera = this.xSelectedPos - ig.system.width /2;
		}
		
		if (targetY < cameraTopBound || targetY > cameraBottomBound ){	
			this.yCamera = this.ySelectedPos - ig.system.height /2;
		}		

	},

	
	//Use for calculating how many tokens player acquired in a level prior to dying.
	lastTokens: null,
	gData:{
		"turnNumber": 1,
		"lvl":1
	},

	startMatchMakingGame: function(playerTwo){
		this.menuScreenNum = 3;
		if (playerTwo){
			playerNum = 2;
			setUpGame();
		}
		else{
			playerNum = 1;
		}
		console.log('ig.game.currentGameId = ' + ig.game.currentGameId);
		checkIfGameIsReadyInterval = setInterval(() => isGameReady(ig.game.currentGameId), 5000);
	},
	playMusicBro: function(which){

		if(which == 1){
			ig.game.musicLevel = .25;
			ig.music.play(01);	
		}
		else if(which == 2){
			ig.game.musicLevel = .25;
			ig.music.play(02);	
		}
		else if(which == 3){
			ig.game.musicLevel = .25;
			ig.music.play(03);	
		}
		else if(which == 4){
			ig.game.musicLevel = .25;
			ig.music.play(04);	
		}
	
		if (!ig.game.muteGame){
			ig.music.volume = ig.game.musicLevel;
		}
		else{
			ig.music.volume = 0;
		}
		
	},
	drawStatusNumbers: function(){
		var ctx = ig.system.context;
		this.statusNumberNames.forEach((entName) => {
			var snnEnt = ig.game.getEntityByName(entName);
			var snnEntPosX = snnEnt.pos.x - ig.game.screen.x;
			var snnEntPosY = snnEnt.pos.y - ig.game.screen.y;
			var color = "#FF69B4";
			var symbol = "-";
			if (snnEnt.theActionType == "Boost" || snnEnt.theActionType == "Support" || snnEnt.theActionType == "R Support" ){
				color = "#33FF33";
				symbol = "+";
			}
			var effectAmount = snnEnt.theEffect;
			if (effectAmount == 0){
				symbol = "";
				effectAmount = "Missed";
			}
			else if (effectAmount == "Killed"){
				symbol = "";
			}
			var myTxt = symbol + effectAmount;
			var myTxtWidth = ctx.measureText(myTxt).width;
			var myTxtX = snnEntPosX + 64 - (myTxtWidth / 2);
			this.dFonts.changeFont(ctx, 3);
			ctx.fillStyle = color;
			ctx.fillText(myTxt, myTxtX, snnEntPosY + 32);			
		});
	},
	drawCharacterHealthBars: function(){
		var ctx = ig.system.context;
		
		ctx.globalAlpha = .33;
		ig.game.chbIndex = 0;
		this.charHealthBarsToDraw.forEach((dboVar) => {
			ig.game.chbIndex++;
			var tileOfCharacter = this[dboVar].myTile;
			var chID =  this[dboVar].character_id;
			var tileName = "tn" + tileOfCharacter;
			var tile = ig.game.getEntityByName(tileName);
			if (tile){
				ig.game.healthBarPosX[`${chID}`] = tile.pos.x - ig.game.screen.x + (tile.size.x * .1);
				ig.game.healthBarWidth[`${chID}`]  = tile.size.x * .8;
				ig.game.healthBarHeight[`${chID}`] = tile.size.y * .1;
				ig.game.healthBarPosY[`${chID}`]  = tile.pos.y - ig.game.screen.y - (tile.size.y / 2);
			}
			var healthBarPosX = ig.game.healthBarPosX[`${chID}`];
			var healthBarWidth = ig.game.healthBarWidth[`${chID}`];
			var healthBarHeight = ig.game.healthBarHeight[`${chID}`];
			var healthBarPosY = ig.game.healthBarPosY[`${chID}`];
			var outLineWidth = 3;
			var currentHealth = this[dboVar].health
			var startingHealth = this[dboVar].startingHealth;
			var halfHealth = startingHealth * .5;
			var quarterHealth = startingHealth * .25;
			var innerBarX = healthBarPosX + outLineWidth;
			var innerBarMaxWidth = healthBarWidth - outLineWidth * 2;
			var innerBarWidth = innerBarMaxWidth * (currentHealth / startingHealth);
			var innerBarY = healthBarPosY + outLineWidth;
			var innerBarHeight = healthBarHeight - outLineWidth * 2;
			
			
			
			//Outer Bar
			if (this[dboVar].health > halfHealth){
				this.drawABox(healthBarPosX, healthBarPosX + healthBarWidth, healthBarPosY, healthBarPosY + healthBarHeight, outLineWidth, "#FFFFFF", true, "#FFFFFF");
			}
			else if (this[dboVar].health > quarterHealth){
				this.drawABox(healthBarPosX, healthBarPosX + healthBarWidth, healthBarPosY, healthBarPosY + healthBarHeight, outLineWidth, "#FF6600", true, "#FFFFFF");
			}
			else{
				this.drawABox(healthBarPosX, healthBarPosX + healthBarWidth, healthBarPosY, healthBarPosY + healthBarHeight, outLineWidth, "#FF0000", true, "#FFFFFF");
			}
			
			//Inner Bar
			if (this[dboVar].health > halfHealth){
				this.drawABox(innerBarX, innerBarX + innerBarWidth, innerBarY, innerBarY + innerBarHeight, 0, "#33FF33", true, "#33FF33");
			}
			else if (this[dboVar].health > quarterHealth){
				this.drawABox(innerBarX, innerBarX + innerBarWidth, innerBarY, innerBarY + innerBarHeight, 0, "#FFD700", true, "#FFD700");
			}
			else{
				this.drawABox(innerBarX, innerBarX + innerBarWidth, innerBarY, innerBarY + innerBarHeight, 0, "#FF69B4", true, "#FF69B4");
			}
			
		});
		
		ctx.globalAlpha = 1;

	},
	drawHUD: function(){
		var ctx = ig.system.context;
		
		//Dont fade clock!
		var storedOpacity = ctx.globalAlpha; 
		ctx.globalAlpha = 1;
		
		this.dFonts.changeFont(ctx, 4);
		ctx.fillStyle = "#FFFFFF";
		
		var myTxt = "Turn #" + this.gData.turnNumber;
		var myTxtWidth = ctx.measureText(myTxt).width;
		var myTxt1X = ig.system.width - myTxtWidth - 20 - 84; //84 is mute button
		ctx.fillText(myTxt, myTxt1X, 33);
		
		var tleft = ig.game.timeLeftInTurn.delta() * -1;
		myTxt = "Time Left: " + tleft.toFixed(2);
		if (tleft.toFixed(2) < 0){
			myTxt = "Time Left: Out of Time";
		}
		if (ig.game.turnReporting){
			myTxt = "Time Left: (reporting)";
		}
		if ( ig.game.timeLeftInTurn.delta() > -10){
		
		}
		ctx.fillStyle = ig.game.timeLeftInTurn.delta() > -15 ? "#FFD700" : "#FFFFFF";
		if ( ig.game.timeLeftInTurn.delta() > -7){
			ctx.fillStyle = "#FF69B4";
		}
		ctx.fillText(myTxt, 30, 33);
		
		//Restore Opacity
		ctx.globalAlpha = storedOpacity;
	},
	setFontSizeHUD: function(){
		var ctx = ig.system.context;
		
		if ( ig.system.width <= 500){
			this.dFonts.setTxtSizeHUD(ctx, 1.2);
		}
		else{
			this.dFonts.setTxtSizeHUD(ctx, .75);
		}
	},
	playMintRedCoinsSound: function(){
		if (!ig.game.muteGame){	
			this.mintRedCoinsSound.volume = .5;
			this.mintRedCoinsSound.play();
		}
	},
	playSwordSound: function(){
		if (!ig.game.muteGame){	
			this.drawSwordSound.volume = .4;
			this.drawSwordSound.play();
		}
	},
	playPickSound: function(){
		if (!ig.game.muteGame){	
			this.pickSound.volume = .1;
			this.pickSound.play();
		}
	},
	playUnpickSound: function(){
		if (!ig.game.muteGame){	
			this.unpickSound.volume = .1;
			this.unpickSound.play();
		}
	},
	playDeployCharacterSound: function(){
		if (!ig.game.muteGame){	
			this.deployCharacterSound.volume = .4;
			this.deployCharacterSound.play();
		}
	},
	playOpenSound: function(){
		if (!ig.game.muteGame){	
			this.openSound.volume = .4;
			this.openSound.play();
		}
	},
	playCloseSound: function(){
		if (!ig.game.muteGame){	
			this.closeSound.volume = .4;
			this.closeSound.play();
		}
	},
	playMissSound: function(){
		if (!ig.game.muteGame){	
			this.missSound.volume = .4;
			this.missSound.play();
		}
	}, 
	playSupportHitSound: function(){
		if (!ig.game.muteGame){	
			this.supportHitSound.volume = .4;
			this.supportHitSound.play();
		}
	}, 
	playMeleeHitSound: function(){
		if (!ig.game.muteGame){	
			this.meleeHitSound.volume = .4;
			this.meleeHitSound.play();
		}
	}, 
	playDrainHitSound: function(){
		if (!ig.game.muteGame){	
			this.drainHitSound.volume = .4;
			this.drainHitSound.play();
		}
	}, 
	playRangedHitSound: function(){
		if (!ig.game.muteGame){	
			this.rangedHitSound.volume = .4;
			this.rangedHitSound.play();
		}
	}, 
	playDeadGuyActingSound: function(){
		if (!ig.game.muteGame){	
			this.deadGuyActingSound.volume = .4;
			this.deadGuyActingSound.play();
		}
	},
	playKillCharacterSound: function(){
		if (!ig.game.muteGame){	
			this.killCharacterSound.volume = .4;
			this.killCharacterSound.play();
		}
	},
	unselectAllTiles: function(){
		var tiles = ig.game.getEntitiesByType(EntityCharacterpiece);
		if (tiles.length > 0){
			tiles.forEach(function(piece){
				tiles.selected = false;
			});
		}
	},
	newRoundEntities: function(){
		var pieces = ig.game.getEntitiesByType(EntityCharacterpiece);
		if (pieces.length > 0){
			pieces.forEach(function(piece){
				piece.newRoundMe();
			});
		}
	},
	//newRoundMe
	//var target = ig.game.getEntitiesByType(EntityRobotFence);
	findLongestWord: function(str){
		var words = str.split(' ');
		var longestWord = '';

		for (var word of words){
			if (word.length > longestWord.length){
				longestWord = word;
			}
		}
		return longestWord;
	},
	setCardDisplay: function(playerNum, characterNum){
				
		var crdImg = false;
		if (characterNum == 1){
			crdImg = playerNum == 1 ? ig.game.p1CardImage0 : ig.game.p2CardImage0;
		}
		else if (characterNum == 2){
			crdImg = playerNum == 1 ? ig.game.p1CardImage1 : ig.game.p2CardImage1;
		}
		else if (characterNum == 3){
			crdImg = playerNum == 1 ? ig.game.p1CardImage2 : ig.game.p2CardImage2;
		}
		
		//Card IMG
		ig.game.cardImg = crdImg;
		
		//Card Stats
		var dboVar = `p${playerNum}C${characterNum}data`;
		ig.game.displayCardDBO = dboVar;
		this.displayCardName = ig.game[dboVar].card_name;
		this.longestWordInCardName = this.findLongestWord(this.displayCardName);
		
		this.displayCardHealth = ig.game[dboVar].health;
		this.displayCardEnergy = ig.game[dboVar].energy;
		this.displayCardAim = ig.game[dboVar].aim;
		this.displayCardSpeed = ig.game[dboVar].speed;
		this.displayCardDefend = ig.game[dboVar].defend;
		this.displayCardLuck = ig.game[dboVar].luck;
		
		this.actorEnergy = ig.game[dboVar].energy;
		this.action1 = ig.game[dboVar].a1_name;
		this.action1cost = ig.game[dboVar].a1_cost;
		this.action1type = ig.game[dboVar].a1_type;
		
		this.action2 = ig.game[dboVar].a2_name;
		this.action2cost = ig.game[dboVar].a2_cost;
		this.action2type = ig.game[dboVar].a2_type;
		
		this.action3 = ig.game[dboVar].a3_name;
		this.action3cost = ig.game[dboVar].a3_cost;
		this.action3type = ig.game[dboVar].a3_type;
		
		//Current Character Location
		this.charCurLoc = ig.game[dboVar].location;
		
		ig.game.spawnEntity( EntityClosecarddisplay, 0, 0);
		if (ig.game.lookingAtMyCharacter){
			ig.game.spawnEntity( EntityCharacteraction, 0, 0);
		}
	},
	/*EXAMPLE CHARACTER DATA. THIS IS POPULATED FROM DATABASE AFTER THE MATCHMAKING SCRIPTS HAVE RUN
	p2C1data{
	  "character_id": 112,
	  "game_id": 19,
	  "player": "p2",
	  "card_id": "c1",
	  "card_name": "Sir Nibblet Crossfield",
	  "health": 30,
	  "energy": 5,
	  "aim": 60,
	  "speed": 85,
	  "defend": 20,
	  "luck": 20,
	  "h_status": "none",
	  "hs_int": 0,
	  "t_status": "none",
	  "v_status": "none",
	  "a1_name": "Holy Cross",
	  "a1_type": "Melee",
	  "a1_trait": "health",
	  "a1_effect": 70,
	  "a1_cost": 4,
	  "a2_name": "Squeek Slash",
	  "a2_type": "Melee",
	  "a2_trait": "health",
	  "a2_effect": 30,
	  "a2_cost": 2,
	  "a3_name": "Nibble",
	  "a3_type": "Melee",
	  "a3_trait": "health",
	  "a3_effect": 10,
	  "a3_cost": 1,
	  "location": 0
	},
	*/

	drawGameOverScreen: function(){
		var ctx = ig.system.context;
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var winningStatement = pNum == ig.game.playerWon ? "You win!" : "You lost!";
		var followUpStatement = pNum == ig.game.playerWon ? "You earned 3 red coins. On to the next one." : "Hope it goes better for you next time. You still earned a red coin.";
		
		var boxT = 100;
		var boxHeight = ig.system.height - 200;
		var boxWidth = ig.system.width * .8;
		var boxLeft = ig.system.width * .1;
		
		
		this.drawABox(boxLeft, boxLeft + boxWidth, boxT, boxT + boxHeight, 5, "#33FF33", true, "#000000");
		
		
		this.dFonts.changeFont(ctx, 6);
		ctx.fillStyle = "#33FF33";
		
		var titleY = boxT + ig.game.curLineHeight + 25;
		var titleX = boxLeft + 25;
		var titleWidth = boxWidth - 50;
		
		this.dFonts.wrapTheText(ctx, winningStatement, titleX, titleY, titleWidth, ig.game.thinLineHeight);
		
		var followUpY = this.dFonts.cursorPosYNewLine + ig.game.curLineHeight + 20;
		
		this.dFonts.changeFont(ctx, 4);
		ctx.fillStyle = "#33FF33";
		
		this.dFonts.wrapTheText(ctx, followUpStatement, titleX, followUpY, titleWidth, ig.game.thinLineHeight);
	
		
		if (ig.game.readyToEnd && this.flashMsg){
			this.dFonts.wrapTheText(ctx, "Click Anywhere to Continue", titleX, boxHeight + boxT - ig.game.curLineHeight - 30, titleWidth, ig.game.thinLineHeight);
		}
		
		
	},
	drawCardDisplay: function(){
		var ctx = ig.system.context;
		
		//Draw the Box
		var boxL = ig.system.width - 468;
		ig.game.cdbl = boxL;
		var boxR = ig.system.width - 20;
		var boxWidth = 448;
		var boxMX = boxL + (boxWidth / 2);
		
		var boxT = 60;
		var boxB = 636;
		var boxHeight = boxB - boxT;
		
		var boxBGcolor = ig.game.lookingAtMyCharacter ? "#000000" : "#5d5d5d";
		
		this.drawABox(boxL, boxR, boxT, boxB, 5, "#343434", true, boxBGcolor);
		
		//Draw the IMG
		var cardImg = new ig.Image(ig.game.cardImg.src);
		var imgX = boxL + 15;
		var imgY = boxT + 15;
		cardImg.draw(imgX, imgY);
		
		//Draw Close Button
		
		this.dFonts.changeFont(ctx, 4);
		ctx.fillStyle = "#FF6600";
		//Close Card Display Button
		ig.game.ccdbX = boxR - 30;
		ig.game.ccdbY = boxT;
		ig.game.ccdbWidth = 50;
		ig.game.ccdbHeight = 50;
		
		ctx.fillText("X", ig.game.ccdbX, ig.game.ccdbY + ig.game.vThinLineHeight);
		
		
		//Draw Name
		this.dFonts.changeFont(ctx, 4);
		ctx.fillStyle = "#33FF33";
		
		//Shrink Font if Title Is Too Big
		var titleWidth = boxWidth - 168;
		var myTxtWidth = ctx.measureText(this.longestWordInCardName).width;
				
		if (myTxtWidth > titleWidth){
			this.dFonts.changeFont(ctx, 3);
			ctx.fillStyle = "#33FF33";
		}
		
		//wrapTheText: function(context, text, x, y, maxWidth, lineHeight){
		//Write the Title
		this.dFonts.wrapTheText(ctx, this.displayCardName, boxL + 153, imgY + ig.game.curLineHeight, titleWidth, ig.game.vThinLineHeight);
		
		//Calc Stats
		this.dFonts.changeFont(ctx, 3);
		
		
		//Set Stat Location Variables
		var yBuffer = ig.game.curLineHeight * 1.35;
		
		var healthTxt = `Health: ${this.displayCardHealth}`;
		var healthTxtY = imgY + 158 + yBuffer;
		
		var energyTxt = `Energy: ${this.displayCardEnergy}`;
		var energyTxtY = healthTxtY + yBuffer;
		
		var aimTxt = `Aim: ${this.displayCardAim}`;
		var aimTxtY = energyTxtY + yBuffer;
		
		var speedTxt = `Speed: ${this.displayCardSpeed}`;
		var speedTxtY = aimTxtY + yBuffer;
		
		var defendTxt = `Defend: ${this.displayCardDefend}`;
		var defendTxtY = speedTxtY + yBuffer;
		
		var luckTxt = `Luck: ${this.displayCardLuck}`;
		var luckTxtY = defendTxtY + yBuffer; 
		
		
		//Draw character buttons (need to go here for two column test.)
		if (ig.game.displayCardView != 2 && ig.game.lookingAtMyCharacter){
			
			if (ig.game[ig.game.displayCardDBO].hasDeployed){
				var dboVar = ig.game.selectedPieceDBOVar;
				var actorID = `ch${ig.game[dboVar].character_id}`;
				var actorEnt = ig.game.getEntityByName(actorID);
				if (actorEnt.hasActed){
					this.drawCharActionButton(imgX, boxB, boxWidth, boxHeight, false);
				}
				else{
					this.drawCharActionButton(imgX, boxB, boxWidth, boxHeight, true);
				}
			}
			else{
				this.drawCharActionButton(imgX, boxB, boxWidth, boxHeight, false);
			}
		}
					
		if (ig.game.displayCardView == 1){
			//Write Stats
			ctx.fillStyle = "#33FF33";
			
			//Display Stats in Two Columns if Top of Top Button is Over Bottom Stat Text
			var twoColumn = ig.game.chActButY < luckTxtY + yBuffer ? true : false;
			
			if (twoColumn){
				ctx.fillText(healthTxt, imgX, healthTxtY);
				ctx.fillText(energyTxt, imgX, energyTxtY);
				ctx.fillText(aimTxt, imgX, aimTxtY);
				ctx.fillText(speedTxt, boxMX, healthTxtY);
				ctx.fillText(defendTxt, boxMX, energyTxtY);
				ctx.fillText(luckTxt, boxMX, aimTxtY);
			}
			else{
				ctx.fillText(healthTxt, imgX, healthTxtY);
				ctx.fillText(energyTxt, imgX, energyTxtY);
				ctx.fillText(aimTxt, imgX, aimTxtY);
				ctx.fillText(speedTxt, imgX, speedTxtY);
				ctx.fillText(defendTxt, imgX, defendTxtY);
				ctx.fillText(luckTxt, imgX, luckTxtY);
			}
		}
		else if (ig.game.displayCardView == 2){
			
			//Write Action Names
			this.dFonts.changeFont(ctx, 3);
			ctx.fillStyle = "#33FF33";
			
			ig.game.actionPosX = imgX;
			ig.game.actionTxtWidth = boxWidth - 40;
			
			//Action 1
			ctx.fillStyle = ig.game.act1Hover ? "#FFD700" : "#33FF33";
			ig.game.action1posY = healthTxtY;
			ctx.fillStyle = parseInt(this.action1cost) > parseInt(this.actorEnergy) ? "#343434" : ctx.fillStyle; //Grey out if cant afford
			ig.game.canAffordAction1 = parseInt(this.action1cost) <= parseInt(this.actorEnergy) ? true : false;
			this.dFonts.wrapTheText(ctx, `${this.action1} (${this.action1cost})`, imgX, ig.game.action1posY + ig.game.curLineHeight, boxWidth - 40, ig.game.vThinLineHeight);
			ig.game.action1Height = this.dFonts.cursorPosYNewLine - ig.game.action1posY;
			//Action 2
			ctx.fillStyle = ig.game.act2Hover ? "#FFD700" : "#33FF33";
			ig.game.action2posY = this.dFonts.cursorPosYNewLine;
			ctx.fillStyle = parseInt(this.action2cost) > parseInt(this.actorEnergy) ? "#343434" : ctx.fillStyle; //Grey out if cant afford
			ig.game.canAffordAction2 = parseInt(this.action2cost) <= parseInt(this.actorEnergy) ? true : false;
			this.dFonts.wrapTheText(ctx, `${this.action2} (${this.action2cost})`, imgX, ig.game.action2posY + ig.game.curLineHeight, boxWidth - 40, ig.game.vThinLineHeight);
			ig.game.action2Height = this.dFonts.cursorPosYNewLine - ig.game.action2posY;
			
			//Action 3
			ctx.fillStyle = ig.game.act3Hover ? "#FFD700" : "#33FF33";
			ig.game.action3posY = this.dFonts.cursorPosYNewLine;
			

			ctx.fillStyle = parseInt(this.action3cost) > parseInt(this.actorEnergy) ? "#343434" : ctx.fillStyle; //Grey out if cant afford
			ig.game.canAffordAction3 = parseInt(this.action3cost) <= parseInt(this.actorEnergy) ? true : false;		
			this.dFonts.wrapTheText(ctx, `${this.action3} (${this.action3cost})`, imgX, ig.game.action3posY + ig.game.curLineHeight, boxWidth - 40, ig.game.vThinLineHeight);
			ig.game.action3Height = this.dFonts.cursorPosYNewLine - ig.game.action3posY;
			
			ctx.fillStyle = "#33FF33";
		}
	},

	drawCharActionButton: function(x, yBottom, maxWidth, wholeUnitHeight, displayActive){
		var ctx = ig.system.context;
		
		ig.game.chActButX = x;
		ig.game.chActButWidth = maxWidth - 30;
		ig.game.chActButHeight = 100;
		ig.game.chActButY = yBottom - ig.game.chActButHeight - 15;	

		var butTxt = "Actions";
		if (ig.game.chActHover && displayActive){
			this.drawButton(ig.game.chActButX, ig.game.chActButY, ig.game.chActButWidth, ig.game.chActButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else if (displayActive){
			this.drawButton(ig.game.chActButX, ig.game.chActButY, ig.game.chActButWidth, ig.game.chActButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)
		}
		else{
			this.drawButton(ig.game.chActButX, ig.game.chActButY, ig.game.chActButWidth, ig.game.chActButHeight, butTxt, 3, "#C0C0C0", "#FFFFFF", "#343434", 3)
		}
	},
	drawTurnEndedScreen: function(){
		var ctx = ig.system.context;
		ctx.globalAlpha = .75;
		//Make everything behind the confirm box darker...
		this.drawABox(0, ig.system.width, 0, ig.system.height, 1, "#000000", true, "#000000");
		ctx.globalAlpha = 1; //Restore opacity
		if (ig.game.turnEndedScreen == 1){
			this.dFonts.changeFont(ctx, 4);
						
			var myTxt = `Turn Ended: ${ig.game.turnEndScreenStatusTxt}`;
			var myTxtWidth = ctx.measureText(myTxt).width;
			var myTxtX = (ig.system.width / 2) - (myTxtWidth / 2);
			
			ctx.fillStyle = "#33FF33";
			ctx.fillText(myTxt, myTxtX, ig.system.height * .33);
		}
	},
	drawTurnReportingScreen: function(){
		var ctx = ig.system.context;

		this.dFonts.changeFont(ctx, 4);
						
		var myTxt = `Reporting on Turn`;
		var myTxtWidth = ctx.measureText(myTxt).width;
		var myTxtX = (ig.system.width / 2) - (myTxtWidth / 2);
			
		ctx.fillStyle = "#33FF33";
		ctx.fillText(myTxt, myTxtX, ig.system.height * .15);
		
		//Turn Reporting Screen
		var trsBoxL = 30;
		var trsBoxR = ig.system.width - (trsBoxL * 2);
		var trsBoxHeight = 360;
		var trsBoxWidth = trsBoxR - trsBoxL;
		var trsBoxB = ig.system.height - 30;
		var trsBoxT = trsBoxB - trsBoxHeight;
		 //Second number is box height
		ctx.globalAlpha = .66;
		this.drawABox(trsBoxL, trsBoxR, trsBoxT, trsBoxB, 3, "#33FF33", true, "#000000");
		ctx.globalAlpha = 1; //Restore opacity
		
		this.dFonts.changeFont(ctx, 2);
		ctx.fillStyle = "#33FF33";

		ig.game.trsLine1 = trsBoxT + ig.game.curLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt1, trsBoxL + 30, ig.game.trsLine1, trsBoxWidth, ig.game.vThinLineHeight);		
		
		ig.game.trsLine2 = this.dFonts.cursorPosYNewLine + ig.game.thinLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt2, trsBoxL + 30, ig.game.trsLine2, trsBoxWidth, ig.game.vThinLineHeight);
		
		ig.game.trsLine3 = this.dFonts.cursorPosYNewLine + ig.game.thinLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt3, trsBoxL + 30, ig.game.trsLine3, trsBoxWidth, ig.game.vThinLineHeight);

		ig.game.trsLine4 = this.dFonts.cursorPosYNewLine + ig.game.thinLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt4, trsBoxL + 30, ig.game.trsLine4, trsBoxWidth, ig.game.vThinLineHeight);
		
		ig.game.trsLine5 = this.dFonts.cursorPosYNewLine + ig.game.thinLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt5, trsBoxL + 30, ig.game.trsLine5, trsBoxWidth, ig.game.vThinLineHeight);
		
		ig.game.trsLine6 = this.dFonts.cursorPosYNewLine + ig.game.thinLineHeight;
		this.dFonts.wrapTheText(ctx, this.moveReportingTxt6, trsBoxL + 30, ig.game.trsLine6, trsBoxWidth, ig.game.vThinLineHeight);
	},
	drawConfirmationBox: function(txt, txtSize){
		var ctx = ig.system.context;
		//drawABox: function(lx, rx, ty, by, lineWidth, lineColor, fill, fillcolor){
		
		ctx.globalAlpha = .66;
		//Make everything behind the confirm box darker...
		this.drawABox(0, ig.system.width, 0, ig.system.height, 1, "#000000", true, "#000000");
		ctx.globalAlpha = 1; //Restore opacity
		
		//Location Variables
		var width = ig.system.width * .66;
		var height = ig.system.height * .66;
		var lx = (ig.system.width * .5) - (width / 2);
		var rx = (ig.system.width * .5) + (width / 2);
		var ty = (ig.system.height * .5) - (height / 2);
		var by = (ig.system.height * .5) + (height / 2);
		var txtMargin = ig.system.width * .05;
				
		//Draw Confirmation Box
		this.drawABox(lx, rx, ty, by, 5, "#343434", true, "#000000");
		this.dFonts.changeFont(ctx, txtSize);
		ctx.fillStyle = "#33FF33";
		//wrapTheText: function(context, text, x, y, maxWidth, lineHeight){
		//Write the Text on the Box
		this.dFonts.wrapTheText(ctx, txt, lx + txtMargin, ty + txtMargin, width - (txtMargin * 2), ig.game.thinLineHeight);
		
		//Var Confirm Button Sizes
		ig.game.confirmButWidth = width * .25;
		ig.game.confirmButHeight = height * .2;

		ig.game.confirmButY = by - (ig.game.confirmButHeight * 1.5);
		ig.game.yConfirmButX = lx + (ig.game.confirmButWidth * .5);
		ig.game.nConfirmButX = rx - (ig.game.confirmButWidth * 1.5);
		
		//Draw Button Boxes
		this.drawABox(ig.game.yConfirmButX, ig.game.yConfirmButX + ig.game.confirmButWidth, ig.game.confirmButY, ig.game.confirmButY + ig.game.confirmButHeight, 4, "#343434", true, "#33FF33");
		this.drawABox(ig.game.nConfirmButX, ig.game.nConfirmButX + ig.game.confirmButWidth, ig.game.confirmButY, ig.game.confirmButY + ig.game.confirmButHeight, 4, "#343434", true, "#33FF33");
		
		//Draw Words on Boxes
		this.dFonts.changeFont(ctx, 5);
		ctx.fillStyle = "#000000";
		
		var myTxt1 = "YES";
		var myTxt1Width = ctx.measureText(myTxt1).width;
		var myTxt1X = (ig.game.yConfirmButX + ig.game.confirmButWidth / 2) - (myTxt1Width / 2);
		ctx.fillText(myTxt1, myTxt1X, ig.game.confirmButY + ig.game.curLineHeight * 1.2);
		
		var myTxt2 = "NO";
		var myTxt2Width = ctx.measureText(myTxt2).width;
		var myTxt2X = (ig.game.nConfirmButX + ig.game.confirmButWidth / 2) - (myTxt2Width / 2);
		ctx.fillText(myTxt2, myTxt2X, ig.game.confirmButY + ig.game.curLineHeight * 1.2);
	},
	drawAlertBox: function(txt, txtSize){
		var ctx = ig.system.context;
		
		ctx.globalAlpha = .66;
		//Make everything behind the confirm box darker...
		this.drawABox(0, ig.system.width, 0, ig.system.height, 1, "#000000", true, "#000000");
		ctx.globalAlpha = 1; //Restore opacity
		
		//Location Variables
		var width = ig.system.width * .66;
		var height = ig.system.height * .66;
		var lx = (ig.system.width * .5) - (width / 2);
		var rx = (ig.system.width * .5) + (width / 2);
		var ty = (ig.system.height * .5) - (height / 2);
		var by = (ig.system.height * .5) + (height / 2);
		var txtMargin = ig.system.width * .05;
				
		//Draw Alert Box
		this.drawABox(lx, rx, ty, by, 5, "#343434", true, "#000000");
		this.dFonts.changeFont(ctx, txtSize);
		ctx.fillStyle = "#33FF33";

		this.dFonts.wrapTheText(ctx, txt, lx + txtMargin, ty + txtMargin, width - (txtMargin * 2), ig.game.thinLineHeight);
		
		//Var Confirm Button Sizes
		ig.game.alertButWidth = width * .25;
		ig.game.alertButHeight = height * .2;

		ig.game.alertButY = by - (ig.game.alertButHeight * 1.5);
		ig.game.alertButX = (ig.system.width / 2) - (ig.game.alertButWidth * .5);

		
		//Draw Button Boxes
		this.drawABox(ig.game.alertButX, ig.game.alertButX + ig.game.alertButWidth, ig.game.alertButY, ig.game.alertButY + ig.game.alertButHeight, 4, "#343434", true, "#33FF33");
		
		//Draw Words on Boxes
		this.dFonts.changeFont(ctx, 5);
		ctx.fillStyle = "#000000";
		
		var myTxt1 = "OK";
		var myTxt1Width = ctx.measureText(myTxt1).width;
		var myTxt1X = (ig.game.alertButX + ig.game.alertButWidth / 2) - (myTxt1Width / 2);
		ctx.fillText(myTxt1, myTxt1X, ig.game.alertButY + ig.game.curLineHeight * 1.2);
	},
	highlightAllTiles: function(whatWay){
		for (var i = 1; i <= 11; i++){
			ig.game.colorTile(i, whatWay);
		}
	},
	calculateRangeScore: function(playerTile, targetTile){
		playerTile = parseInt(playerTile, 10);
		targetTile = parseInt(targetTile, 10);

		//Map of distances between specific tiles
		const distanceMap = {
			1: { 2: 1, 3: 2, 4: 1, 5: 1, 6: 1, 7: 2, 8: 3, 9: 2, 10: 3, 11: 3 },
			2: { 1: 1, 3: 1, 4: 2, 5: 1, 6: 1, 7: 1, 8: 2, 9: 2, 10: 2, 11: 2 },
			3: { 1: 2, 2: 1, 4: 3, 5: 2, 6: 1, 7: 1, 8: 1, 9: 2, 10: 2, 11: 2 },
			4: { 1: 1, 2: 2, 3: 3, 5: 1, 6: 2, 7: 3, 8: 4, 9: 1, 10: 2, 11: 3 },
			5: { 1: 1, 2: 1, 3: 2, 4: 1, 6: 1, 7: 2, 8: 3, 9: 1, 10: 1, 11: 2 },
			6: { 1: 1, 2: 1, 3: 1, 4: 2, 5: 1, 7: 1, 8: 2, 9: 1, 10: 1, 11: 1 },
			7: { 1: 2, 2: 1, 3: 1, 4: 3, 5: 2, 6: 1, 8: 1, 9: 2, 10: 1, 11: 1 },
			8: { 1: 3, 2: 2, 3: 1, 4: 4, 5: 3, 6: 2, 7: 1, 9: 3, 10: 2, 11: 1 },
			9: { 1: 2, 2: 2, 3: 2, 4: 1, 5: 1, 6: 1, 7: 2, 8: 3, 10: 1, 11: 2 },
			10: { 1: 2, 2: 2, 3: 2, 4: 2, 5: 1, 6: 1, 7: 1, 8: 2, 9: 1, 11: 1 },
			11: { 1: 2, 2: 2, 3: 2, 4: 3, 5: 2, 6: 1, 7: 1, 8: 1, 9: 2, 10: 1 }
		};

		//Use the map to find the distance
		const totalDistance = distanceMap[playerTile][targetTile] || 1; //Default to 4 if not found
		return totalDistance;
	},
	getAdjacentTiles: function(tileNumber){
		const adjacentMapping = {
			1: [2, 4, 5, 6],
			2: [1, 3, 5, 6, 7],
			3: [2, 6, 7, 8],
			4: [1, 5, 9],
			5: [1, 2, 4, 6, 9, 10],
			6: [1, 2, 3, 5, 7, 9, 10, 11],
			7: [2, 3, 6, 8, 10, 11],
			8: [3, 7, 11],
			9: [4, 5, 6, 10],
			10: [5, 6, 7, 9, 11],
			11: [6, 7, 8, 10]
		};
		return adjacentMapping[tileNumber];
	},
	getAdjacentPlayerLocations: function(playerNumber, playerTile) {
		var adjacentTiles = ig.game.getAdjacentTiles(playerTile);
		var thePlayerNumber = playerNumber === 1 ? 1 : 2;
		var playerCharacterInfo = [];

		for (var character = 1; character <= 3; character++) {
			var dataObjectName = `p${thePlayerNumber}C${character}data`;
			var characterData = ig.game[dataObjectName];

			if (characterData) {

				//Convert character location to integer for proper comparison
				var characterLocation = parseInt(characterData.location, 10);

				if (adjacentTiles.includes(characterLocation)) {
					playerCharacterInfo.push({
						characterId: characterData.character_id,
						location: characterLocation
					});
				}
			}
		}

		return playerCharacterInfo;
	},
	getAdjacentEnemyLocations: function(playerNumber, playerTile) {
		var adjacentTiles = ig.game.getAdjacentTiles(playerTile).map(t => parseInt(t, 10)); //Ensure adjacentTiles are integers
		var thePlayerNumber = playerNumber === 1 ? 2 : 1; //Determine the opponent's player number
		var enemyLocations = [];

		for (var character = 1; character <= 3; character++) {
			var dataObjectName = `p${thePlayerNumber}C${character}data`;
			var characterData = ig.game[dataObjectName];
		
			//Ensure characterData.location is converted to an integer before comparison
			if (characterData && adjacentTiles.includes(parseInt(characterData.location, 10))) {
				enemyLocations.push(parseInt(characterData.location)); 
			}
		}
		return enemyLocations;
	},
	highlightAdjacentTiles: function(tileNumber, whatWay){
		const adjacentTiles = this.getAdjacentTiles(tileNumber);
		if (adjacentTiles){
			adjacentTiles.forEach(tile => {
				this.colorTile(tile, whatWay);
			});
		}
		else{
			console.log('adjacentTiles is ' + adjacentTiles);
		} 
	},
	highlightAdjacentEnemyTiles: function(playerNumber, playerTile, whatWay){
		const adjacentEnemyLocations = this.getAdjacentEnemyLocations(playerNumber, playerTile);
		adjacentEnemyLocations.forEach(tile => {
			this.colorTile(tile, whatWay);
		});
	},
	highlightCharTiles: function(playerNumber, whatWay){
		const playerLocations = this.getPieceLocationsOfPlayer(playerNumber);

		playerLocations.forEach(tile => {
			this.colorTile(tile, whatWay);
		});
	},
	deselectTilesWithAttemptedMovesOnThem: function(){
		var attemptedMoves = ig.game.getEntitiesByType(EntityMoveattempt);
		if (attemptedMoves.length > 0){
			attemptedMoves.forEach(function(attempt){
				ig.game.colorTile(attempt.myTile, "invalidMove");
			});
		}
		
	},
	getAllPieceLocationsDebug: function(){
		var locations = [];
		for (var player = 1; player <= 2; player++){
			for (var character = 1; character <= 3; character++){
				var dataObjectName = `p${player}C${character}data`;
				var characterData = ig.game[dataObjectName];
				if (characterData && characterData.location !== undefined){
					locations.push(parseInt(characterData.location));
				}
			}
		}
		return locations;
	},
	getAllPieceLocations: function() {
		var locations = [];
		for (var player = 1; player <= 2; player++) {
			for (var character = 1; character <= 3; character++) {
				var dataObjectName = `p${player}C${character}data`;
				var characterData = ig.game[dataObjectName];
				if (characterData && characterData.location !== undefined) {
					// Convert location to an integer before pushing
					locations.push(parseInt(characterData.location));
				}
			}
		}

		return locations;
	},
	getPieceLocationsOfEnemy: function() {
		var pNum = ig.game.playerNumber === "p1" ? 2 : 1;
		var locations = [];

		for (var character = 1; character <= 3; character++) {
			var dataObjectName = `p${pNum}C${character}data`;
			var characterData = ig.game[dataObjectName];

			if (characterData) {
				if (characterData.location !== undefined) {
					locations.push(parseInt(characterData.location));
				}
			}
		}
		return locations;
	},
	getPieceLocationsOfPlayer: function(playerNumber){
		//var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var locations = [];
		for (var character = 1; character <= 3; character++){
			var dataObjectName = `p${playerNumber}C${character}data`;
			var characterData = ig.game[dataObjectName];
			if (characterData && characterData.location !== undefined){
				locations.push(parseInt(characterData.location));
			}
		}
		return locations;
	},
	/*
	deselectPlayerOccupiedTiles: function(tile, player) {
		//Convert tile to an integer if it's a string
		var tileNumber = parseInt(tile, 10);

		//Get all adjacent tiles as integers
		var adjacentTiles = ig.game.getAdjacentTiles(tileNumber).map(t => parseInt(t, 10));

		//Get adjacent tiles that are occupied by the player
		var playerCharacterInfo = ig.game.getAdjacentPlayerLocations(player, tileNumber);

		//Extract only the location numbers as integers from playerCharacterInfo
		var playerOccupiedTiles = playerCharacterInfo.map(info => parseInt(info.location, 10));

		//Clear colors for tiles that are both adjacent and occupied
		playerOccupiedTiles.forEach(occupiedTile => {
			if (adjacentTiles.includes(occupiedTile)) {
				ig.game.clearOneTileColor(occupiedTile);
			}
		});
	},*/
	determineInitialPlayerOccupiedTiles: function(){
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var playerLoc = this.getPieceLocationsOfPlayer(pNum);
		return playerLoc;
	},
	deselectPlayerOccupiedTiles: function(tile, player, initialOccupiedTiles = []) {
		// Convert tile to an integer if it's a string
		var tileNumber = parseInt(tile, 10);

		// Get all adjacent tiles as integers
		var adjacentTiles = ig.game.getAdjacentTiles(tileNumber).map(t => parseInt(t, 10));

		// Get adjacent tiles that are occupied by the player
		var playerCharacterInfo = ig.game.getAdjacentPlayerLocations(player, tileNumber);

		// Extract only the location numbers as integers from playerCharacterInfo
		var playerOccupiedTiles = playerCharacterInfo.map(info => parseInt(info.location, 10));

		// Combine playerOccupiedTiles and initialOccupiedTiles
		var allOccupiedTiles = [...new Set([...playerOccupiedTiles, ...initialOccupiedTiles])];
		// Clear colors for tiles that are both adjacent and occupied
		allOccupiedTiles.forEach(occupiedTile => {
			if (adjacentTiles.includes(occupiedTile)) {
				ig.game.clearOneTileColor(occupiedTile);
			}
		});
	},
	deselectAllSquares: function(){
		for (var i = 1; i <= 11; i++){
			var tileName = "tn" + i;
			var tile = ig.game.getEntityByName(tileName);

			if (tile){
				tile.selected = false;
				tile.displayAs =  "notselected";
			}
		}
	},
	colorTile: function (whichTile, whatWay){
		var tileName = "tn"+ whichTile;
		var tile = ig.game.getEntityByName(tileName);
		if (tile && whatWay){
			tile.displayAs = whatWay;
		}
		else if (tile){
			tile.displayAs = "notselected";
		}		
	},
	clearTileColors: function(){
		for (var i = 1; i <= 11; i++){
			var tileName = "tn" + i;
			var tile = ig.game.getEntityByName(tileName);

			if (tile){
				tile.displayAs =  "notselected";
			}
		}
	},
	setTileColorsOfArray: function(tileNumbers, displayValue) {
		tileNumbers.forEach(function(tileNum) {
			if (tileNum !== 0) { //Ignore tile number 0
				var tileName = "tn" + tileNum;
				var tile = ig.game.getEntityByName(tileName);

				if (tile) {
					tile.displayAs = displayValue;
				}
			}
		});
	},
	clearOneTileColor: function(which){
		var tileName = "tn" + which;
		var tile = ig.game.getEntityByName(tileName);

		if (tile){
			tile.displayAs =  "notselected";
		}
	},
	spawnAlertBox: function(txt, txtSize, num){
		if (!ig.game.alertBox && !ig.game.confirmBox){
			var myNum = num ? num : false;
			ig.game.alertBox = true;
			ig.game.alertMsg = txt;
			ig.game.alertSize = txtSize;
			ig.game.spawnEntity( EntityOk, 0, 0, { num: myNum });
		}
	},
	spawnConfirmBox: function(txt, txtSize, num){
		if (!ig.game.confirmBox && !ig.game.alertBox){
			var myNum = num ? num : false;
			ig.game.confirmMsg = txt;
			ig.game.confirmSize = txtSize;
			ig.game.confirmBox = true;
			ig.game.spawnEntity( EntityConfirm, 0, 0, { name: "cYes", num: myNum });
			ig.game.spawnEntity( EntityConfirm, 0, 0, { name: "cNo", num: myNum });
		}
	},
	drawABox: function(lx, rx, ty, by, lineWidth, lineColor, fill, fillcolor){
		var ctx = ig.system.context;
		ctx.beginPath();	
		
		ctx.moveTo(lx, ty);
		ctx.lineTo(rx, ty);
		ctx.lineTo(rx, by);
		ctx.lineTo(lx, by);
		ctx.lineTo(lx, ty);
		
		ctx.closePath();
		
		if(lineWidth){
			ctx.lineWidth = lineWidth;
		}
		if (lineColor){
			ctx.strokeStyle = lineColor;
		}
		
		ctx.stroke();
		
		if (fillcolor){
			ig.system.context.fillStyle = fillcolor;
		}
		if (fill == true){
			ctx.fill();	
		}
	},
	drawTitleScreen: function(){
		var ctx = ig.system.context;
		//Pink BG
		this.drawABox(0, ig.system.width, 0, ig.system.height, 0, this.color1, true, this.color1);
		
		
		
		//Draw Title Text Image
		var logoWidth = ig.system.width * .8;
		var logoMargin = ig.system.width * .1;
		var logoHeight = logoWidth / 10;
				
		//Draw Title Image Image
		var imageWidth = ig.system.height * .4;
		var imageHeight = imageWidth;
		var imageX = ig.system.width / 2 - (imageWidth / 2);
		var imageY = ig.system.height * .35;

		var butWidth = 0;
		var butHeight = 0;
		
		var imageY = ig.system.height * .025;
		var buffer = ig.system.height * .025;
		//Portrait
		if (ig.system.height > ig.system.width){
			
			imageY = ig.system.height * .05;
			
			logoWidth = ig.system.width * .7; 
			logoHeight = logoWidth;
			
			butWidth = ig.system.width * .425;
			butHeight = butWidth / 4;
			
			this.ngbX = (ig.system.width / 2) - (butWidth / 1.75);
			this.ngbY = imageY + logoHeight + buffer; //add another imageY as a buffer.
			
			this.ctbX = this.ngbX;
			
			if (ig.system.height > ig.system.width * 1.75){
				this.ctbY = this.ngbY + butHeight + (buffer * 4)
			}
			else{	
				this.ctbY = this.ngbY + butHeight + (buffer * 2);
			}
		}
		//Landscape
		else{
			
			logoWidth = ig.system.height * .7; 
			logoHeight = logoWidth;
			
			butWidth = ig.system.height * .45;
			butHeight = butWidth / 4;
			//We have a continue button because a save file exists
			if (butWidth){
				this.ctbX = (ig.system.width / 2) - (butWidth + buffer );
				this.ngbY = buffer + logoHeight + buffer; 
				
				this.ngbX = (ig.system.width / 2) + buffer * 3;
				//If there is a saved game, we are only drawing one button, so center this one.
				if (!this.savedGame ){	
					if (ig.system.width < this.logoWidthThresh){
						this.ngbX =  (ig.system.width / 2) - (butWidth / 1.75);
					}
					else{
						this.ngbX =  (ig.system.width / 2) - (butWidth / 2.1);
					}
				}
				
				this.ctbY = this.ngbY
			}
			//No save file exists. Start a new game.			
			else{
				this.ngbX = (ig.system.width / 2) - (butWidth / 2);
				this.ngbY = buffer + logoHeight + buffer; 
			
				this.ctbX = this.ngbX;
				this.ctbY = this.ctbY
			}
		}
		
		
		
		
		this.tsButtonWidth = butWidth;
		this.tsButtonHeight = butHeight;
		
		imageX = (ig.system.width / 2) - (logoWidth / 2);
		
		
		//Purple BG Strip
		var pbgWidth = imageWidth * 4;
		var pbgL = (ig.system.width - pbgWidth) / 2;
		this.drawABox(pbgL, pbgL + pbgWidth, 0, ig.system.height, 5, "#343434", true, "#7D4E8D");
		
		//Blue BG Strip
		var blbgWidth = imageWidth * 3.5;
		var blbgL = (ig.system.width - blbgWidth) / 2;
		this.drawABox(blbgL, blbgL + blbgWidth, 0, ig.system.height, 5, "#343434", true, "#1CA2AC");
		
		//Yellow BG Strip		
		var ybgWidth = imageWidth * 3;
		var ybgL = (ig.system.width - ybgWidth) / 2;
		this.drawABox(ybgL, ybgL + ybgWidth, 0, ig.system.height, 5, "#343434", true, "#FFD700");
		
		//Orange BG Strip		
		var obgWidth = imageWidth * 2.5;
		var obgL = (ig.system.width - obgWidth) / 2;
		this.drawABox(obgL, obgL + obgWidth, 0, ig.system.height, 5, "#343434", true, "#FF6600");
		
		//Grey BG Strip
		var bbgWidth = imageWidth * 2;
		var bbgL = (ig.system.width - bbgWidth) / 2;
		this.drawABox(bbgL, bbgL + bbgWidth, 0, ig.system.height, 5, "#343434", true, "#C0C0C0");
		
		
		
			
		ctx.drawImage(this.tsImage, imageX, imageY, logoWidth, logoHeight );
		ctx.drawImage(this.newGameButton, this.ngbX, this.ngbY, butWidth, butHeight );
		if (this.savedGame ){	
			ctx.drawImage(this.continueButton, this.ctbX, this.ctbY, butWidth, butHeight );
		}
		
		
		//this.dFonts.changeFont(ctx, 5);
		ctx.fillStyle = this.color2;

		/*var txt =  'Click Anywhere to Start';
		var xPos = logoMargin * 1.2;
		var yPos = imageY + imageHeight * 1.25;
		//this.dFonts.wrapTheText(ctx, txt,  ig.system.width * .1, ig.system.width * .8, ig.system.width * .5, this.vmin * 6);
		this.dFonts.wrapTheText(ctx, txt, xPos, yPos, ig.system.width - (xPos * 2) , this.dFonts.vmin * 6);*/
		
	},
	drawMenuScreen: function(){
		var ctx = ig.system.context;
		//Draw Background			//drawABox: function(lx, rx, ty, by, lineWidth, lineColor, fill, fillcolor){
		var pinkLineSize = 20;
		this.drawABox(0, ig.system.width, 0, ig.system.height, 0, "#FF69B4", true, "#FF69B4"); //Background Frame
		this.drawABox(pinkLineSize, ig.system.width - pinkLineSize, 0, ig.system.height, 0, "#C0C0C0", true, "#C0C0C0");
		var tFrameSize = 10;
		var tBuffer = 80;
		var tAdjust = tFrameSize + tBuffer;
		this.drawABox(tAdjust, ig.system.width - tAdjust, tAdjust, ig.system.height - tAdjust, tFrameSize, "#343434", true, "#000000");
		 //Welcome Menu
		//Title Variables
		  //Welcome Menu
		if (this.menuScreenNum == 2){
			/*if (ig.game.playersWaitingNum == 0){
				tTxt = `Entering into the Waiting Room`;
			}
			else{
				tTxt = "Waiting Room";	
			}*/
			tTxt = "Waiting Room";	
		}
		
		else if (this.menuScreenNum == 3){
			tTxt = "Match Found!";
		}
		
		var tx = tAdjust;
		var ty = tAdjust + ig.game.curLineHeight + 20;
		ig.game.tx = tx;
		ig.game.ty = ty;
		ig.game.tb = ig.system.height - tAdjust;
		var tWid = (ig.system.width - tAdjust) - 20;
		ig.game.tWid = tWid;
		
		//Draw Title
		this.drawMenuTitle(ig.game.titleScreenTxt, tx, ty, tWid);
		
		//drawButton: function(x, y, width, height, txt, txtSize){
		var bWidth = ig.system.width * .2;
		var bHeight = bWidth / 2;
		var bTx = ig.system.height * .65;
		
		if (this.menuScreenNum == 1){
			this.drawDeckSelector();
			this.drawMenuButtons();
			this.drawRedCoinsDisp();
		}
		else if (this.menuScreenNum == 2){ //Matchmaking Screen
			this.dFonts.changeFont(ctx, 4);
			ctx.fillStyle = "#33FF33";	
			var mmTxt = `There are ${ig.game.playersWaitingNum} players waiting for matches...`;
			if (ig.game.playersWaitingNum == 0){
				mmTxt = `Looking for players...`;
			}
			else if (ig.game.playersWaitingNum == 1){
				mmTxt = `There is ${ig.game.playersWaitingNum} player waiting for a match...`;
			}
			this.dFonts.wrapTheText(ctx, mmTxt, tx + 50, ig.system.height / 2, tWid - 100, ig.game.thinLineHeight);
			this.drawLeaveMMButton();
		}
		else if (this.menuScreenNum == 3){
			var mmTxt = `Setting up game against player ${ig.game.trimmedOpponentID}.`;
			this.dFonts.changeFont(ctx, 4);
			ctx.fillStyle = "#33FF33";
			this.dFonts.wrapTheText(ctx, mmTxt, tx + 50, ig.system.height / 2, tWid - 100, ig.game.thinLineHeight);
		}
	},
	drawRedCoinsDisp: function(){
		var ctx = ig.system.context;
		
		var redCoinNum = 0;
		
		if (window.userRedCoins){
			redCoinNum = window.userRedCoins;
		}
		
		var myTxt = "Red Coins: " + redCoinNum;
		var myTxtWidth = ctx.measureText(myTxt).width;
		
		ig.game.mintRedCoinButHeight = ig.game.thinLineHeight + 30;
		var rcdX = ig.game.deckButton1X ;
		var rcdY = ig.game.deckButtonY - ig.game.mintRedCoinButHeight - 80;
		

		this.dFonts.changeFont(ctx, 3);
		ctx.fillStyle = "#33FF33";
		ctx.fillText(myTxt, rcdX, rcdY);	
		
		ig.game.mintRedCoinButX = rcdX;
		ig.game.mintRedCoinButY = rcdY + ig.game.thinLineHeight;
		ig.game.mintRedCoinButWidth = ig.game.deckButtonWidth * 3.66;
		
		
		var butTxt = "Mint Redcoins"
		
		if (!redCoinNum){
			this.drawButton(ig.game.mintRedCoinButX, ig.game.mintRedCoinButY, ig.game.mintRedCoinButWidth, ig.game.mintRedCoinButHeight, butTxt, 3, "#000000", "#343434", "#343434", 3)		
		}
		else if (this.mintRedCoinButHover){
			this.drawButton(ig.game.mintRedCoinButX, ig.game.mintRedCoinButY, ig.game.mintRedCoinButWidth, ig.game.mintRedCoinButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.mintRedCoinButX, ig.game.mintRedCoinButY, ig.game.mintRedCoinButWidth, ig.game.mintRedCoinButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)		
		}
		
		
	},
	drawMenuTitle: function(txt, tx, ty, tWid){
		var ctx = ig.system.context;
		this.dFonts.changeFont(ctx, 7);
		ctx.fillStyle = "#33FF33";
		this.dFonts.wrapTheText(ctx, txt, tx + 50, ty + 50, tWid - 100, ig.game.vThinLineHeight);		
	},
	drawLeaveMMButton: function(){
		var ctx = ig.system.context;
		
		ig.game.lmmButX = ig.game.tx + 50;
		ig.game.lmmButWidth = ig.system.width * .25;
		ig.game.lmmButHeight = ig.game.mmButWidth / 2;
		ig.game.lmmButY = ig.game.tb - ig.game.lmmButHeight * 1.25;
		
		var butTxt = "Leave Matchmaking";
		if (ig.game.lmmHover){
			this.drawButton(ig.game.lmmButX, ig.game.lmmButY, ig.game.lmmButWidth, ig.game.lmmButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.lmmButX, ig.game.lmmButY, ig.game.lmmButWidth, ig.game.lmmButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)
		}
	},
	drawCharacterCardButton: function(){
		var ctx = ig.system.context;
		
		var butTxt = "Play Character";
		
		ig.game.ccButWidth = ig.system.width * .2;
		ig.game.ccButHeight = ig.game.mmButWidth / 2;
		if (ig.system.height > ig.system.width * 1.25){
			ig.game.ccButHeight = ig.game.mmButWidth / 3;
		}
		ig.game.ccButX = 40;
		ig.game.ccButY = ig.system.height - ig.game.ccButHeight * 1.25;
		ig.game.openButtonMenuX = ig.game.ccButX + ig.game.ccButWidth;
		ig.game.openButtonMenuYbottom = ig.game.ccButY;
		
		var c1Location = false;
		var c2Location = false;
		var c3Location = false;
		
		if (ig.game.p1C1data){
			c1Location = ig.game.playerNumber == "p1" ? ig.game.p1C1data.location : ig.game.p2C1data.location;
			c2Location = ig.game.playerNumber == "p1" ? ig.game.p1C2data.location : ig.game.p2C2data.location;
			c3Location = ig.game.playerNumber == "p1" ? ig.game.p1C3data.location : ig.game.p2C3data.location;
		}
		//Grey out text if all characters played...
		if (c1Location > 0 && c2Location > 0 && c3Location > 0 ){
			this.drawButton(ig.game.ccButX, ig.game.ccButY, ig.game.ccButWidth, ig.game.ccButHeight, butTxt, 3, "#000000", "#343434", "#343434", 3)
		}
		else if (ig.game.ccButHover){
			this.drawButton(ig.game.ccButX, ig.game.ccButY, ig.game.ccButWidth, ig.game.ccButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.ccButX, ig.game.ccButY, ig.game.ccButWidth, ig.game.ccButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)
		}
		
	},
	drawEndTurnButton: function(){
		var ctx = ig.system.context;
		
		var butTxt = "End Turn";
		
		//Same Size as Draw Character Card Button
		ig.game.etButWidth = ig.game.ccButWidth;
		ig.game.etButHeight = ig.game.ccButHeight;

		ig.game.etButX = ig.game.ccButX + ig.game.etButWidth + 40;
		ig.game.etButY = ig.game.ccButY;
		
		if (ig.game.etButHover){
			this.drawButton(ig.game.etButX, ig.game.etButY, ig.game.etButWidth, ig.game.etButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.etButX, ig.game.etButY, ig.game.etButWidth, ig.game.etButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)
		}
	},
	drawOpenButtonMenu: function(){
		var ctx = ig.system.context;
		
		ctx.globalAlpha = .66;
		//Make everything behind the confirm box darker...
		this.drawABox(0, ig.system.width, 0, ig.system.height, 1, "#000000", true, "#000000");
		ctx.globalAlpha = 1; //Restore opacity
		
		ig.game.openButtonMenuWidth = ig.system.width * .5;
		ig.game.openButtonMenuHeight = ig.system.height * .66;
		ig.game.openButtonMenuY = ig.game.openButtonMenuYbottom - ig.game.openButtonMenuHeight;
		//Some of these positions are set in drawCharacterCardButton() function
		
		this.drawABox(ig.game.openButtonMenuX, ig.game.openButtonMenuX + ig.game.openButtonMenuWidth, ig.game.openButtonMenuY, ig.game.openButtonMenuYbottom, 5, "#33FF33", true, "#000000");
		
		
		//Close Button 
		ig.game.closeOBMButWidth = ig.game.openButtonMenuWidth * .1;
		ig.game.closeOBMButHeight = ig.game.openButtonMenuHeight * .1;
		ig.game.closeOBMButX = ig.game.openButtonMenuX + ig.game.openButtonMenuWidth - ig.game.closeOBMButWidth;
		ig.game.closeOBMButY = ig.game.openButtonMenuY;
		
		this.dFonts.changeFont(ctx, 6);
		ctx.fillStyle = "#FF6600";
		ctx.fillText("X", ig.game.closeOBMButX, ig.game.openButtonMenuY + ig.game.curLineHeight);
		
		if (ig.game.openButtonMenuDisplay == "characterCards"){
			this.drawCharacterCardNames();
		}
		
	},
	getReadyToPlayCharacter: function(){
		var pNum = ig.game.playerNumber == "p1" ? 1: 2;
		var deployTile = pNum == 1 ? 4 : 8;
		
		var c3Name = ig.game.playerNumber == "p1" ? ig.game.p1C3data.card_name : ig.game.p2C3data.card_name;
		
		var c1Location = ig.game.playerNumber == "p1" ? ig.game.p1C1data.location : ig.game.p2C1data.location;
		var c2Location = ig.game.playerNumber == "p1" ? ig.game.p1C2data.location : ig.game.p2C2data.location;
		var c3Location = ig.game.playerNumber == "p1" ? ig.game.p1C3data.location : ig.game.p2C3data.location;
		
		if (c1Location == deployTile || c2Location == deployTile || c3Location == deployTile){
			var msg = `You cannot play a character card until you move your other character out of the way.`;
			ig.game.spawnAlertBox(msg, 5, 99999);
			return false;
		}
		else if ( ig.game.characterDeployed ){
			var msg = `You have already deployed one character card this round. You may deploy another one next turn if you have more.`;
			ig.game.spawnAlertBox(msg, 5, 99999);
			return false;
		}
		else{
			return true;
		}
	},
	drawCharacterCardNames: function(){
		var ctx = ig.system.context;
		
		this.dFonts.changeFont(ctx, 5);
		ctx.fillStyle = "#33FF33";
		
		var hdrTxt = "My Cards";
		var hdrTxtWidth = ctx.measureText(hdrTxt).width;
		var hdrTxtX = ig.game.openButtonMenuX + (ig.game.openButtonMenuWidth / 2) - (hdrTxtWidth / 2);
		var hdrTxtY = ig.game.openButtonMenuY + (ig.game.curLineHeight * 2);
		ctx.fillText(hdrTxt, hdrTxtX, hdrTxtY);
		
		var crdMrgn = ig.game.openButtonMenuWidth * .05;
		
		//CARD 1
		ig.game.OBMcrdX = ig.game.openButtonMenuX + crdMrgn;
		ig.game.OBMcard1Y = hdrTxtY + ig.game.curLineHeight * 1.5;
		ig.game.OBMcrdWidth = ig.game.openButtonMenuWidth - (crdMrgn * 2.1);
		
		this.dFonts.changeFont(ctx, 3);
		
		var c1Name = ig.game.playerNumber == "p1" ? ig.game.p1C1data.card_name : ig.game.p2C1data.card_name;
		var c1Location = ig.game.playerNumber == "p1" ? ig.game.p1C1data.location : ig.game.p2C1data.location;
		
		if (c1Location > 0){
			ctx.fillStyle = "#C0C0C0";
		}
		else if (ig.game.pcBut1Hover){
			ctx.fillStyle = "#FFD700";
		}
		else{
			ctx.fillStyle = "#33FF33";
		}
		this.dFonts.wrapTheText(ctx, c1Name, ig.game.OBMcrdX, ig.game.OBMcard1Y, ig.game.OBMcrdWidth, ig.game.thinLineHeight);	
		ig.game.OBMcrd1Height = this.dFonts.cursorPosYNewLine - ig.game.OBMcard1Y - ig.game.thinLineHeight;
		
		//CARD 2
		ig.game.OBMcard2Y = this.dFonts.cursorPosYNewLine + ig.game.curLineHeight * 1.5;
				
		var c2Name = ig.game.playerNumber == "p1" ? ig.game.p1C2data.card_name : ig.game.p2C2data.card_name;
		var c2Location = ig.game.playerNumber == "p1" ? ig.game.p1C2data.location : ig.game.p2C2data.location;
		
		if (c2Location > 0){
			ctx.fillStyle = "#C0C0C0";
		}
		else if (ig.game.pcBut2Hover){
			ctx.fillStyle = "#FFD700";
		}
		else{
			ctx.fillStyle = "#33FF33";
		}
		
		this.dFonts.wrapTheText(ctx, c2Name, ig.game.OBMcrdX, ig.game.OBMcard2Y, ig.game.OBMcrdWidth, ig.game.thinLineHeight);	
		ig.game.OBMcrd2Height = this.dFonts.cursorPosYNewLine - ig.game.OBMcard2Y - ig.game.thinLineHeight;
		
		//CARD 3
		ig.game.OBMcard3Y = this.dFonts.cursorPosYNewLine + ig.game.curLineHeight * 1.5;
				
		var c3Name = ig.game.playerNumber == "p1" ? ig.game.p1C3data.card_name : ig.game.p2C3data.card_name;
		var c3Location = ig.game.playerNumber == "p1" ? ig.game.p1C3data.location : ig.game.p2C3data.location;
		
		if (c3Location > 0){
			ctx.fillStyle = "#C0C0C0";
		}
		else if (ig.game.pcBut3Hover){
			ctx.fillStyle = "#FFD700";
		}
		else{
			ctx.fillStyle = "#33FF33";
		}
		ig.game.adjustPCButForLineHeight = ig.game.thinLineHeight;
		
		this.dFonts.wrapTheText(ctx, c3Name, ig.game.OBMcrdX, ig.game.OBMcard3Y, ig.game.OBMcrdWidth, ig.game.thinLineHeight);	
		ig.game.OBMcrd3Height = this.dFonts.cursorPosYNewLine - ig.game.OBMcard3Y - ig.game.thinLineHeight;
	},
	drawMenuButtons: function(){
		var ctx = ig.system.context;
		
		ig.game.mmButX = ig.game.tx + 50;
		ig.game.mmButY = ig.system.height * .45;
		ig.game.mmButWidth = ig.system.width * .175;
		ig.game.mmButHeight = ig.game.mmButWidth / 2;
		
		var butTxt = "Matchmaking";
		if (ig.game.mmHover){
			this.drawButton(ig.game.mmButX, ig.game.mmButY, ig.game.mmButWidth, ig.game.mmButHeight, butTxt, 3, "#33FF33", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.mmButX, ig.game.mmButY, ig.game.mmButWidth, ig.game.mmButHeight, butTxt, 3, "#000000", "#33FF33", "#33FF33", 3)
//drawButton(x, y, width, height, txt, txtSize, boxColor, txtColor, borderColor, borderSize){
		}
		
		//ig.game.p1gButX = ig.game.tx + 20 + (ig.game.mmButX * 1.5);
		//ig.game.p1gButY = ig.system.height / 2;
		ig.game.p1gButX = ig.game.mmButX;
		ig.game.p1gButY = ig.game.mmButY + (ig.game.mmButHeight * 1.15)
		ig.game.p1gButWidth = ig.system.width * .175;
		ig.game.p1gButHeight = ig.game.mmButWidth / 2;
		
		butTxt = "Play CPU";
		if (ig.game.p1gHover){
			this.drawButton(ig.game.p1gButX, ig.game.p1gButY, ig.game.p1gButWidth, ig.game.p1gButHeight, butTxt, 3, "#343434", "#000000", "#343434", 3)		
		}
		else{
			this.drawButton(ig.game.p1gButX, ig.game.p1gButY, ig.game.p1gButWidth, ig.game.p1gButHeight, butTxt, 3, "#343434", "#000000", "#343434", 3)
//drawButton(x, y, width, height, txt, txtSize, boxColor, txtColor, borderColor, borderSize){
		}
		
		
	},
	drawDeckSelector: function(){
		
		var ctx = ig.system.context;
		
		ig.game.deckButtonWidth = ig.system.width * .05;
		ig.game.deckButtonHeight = ig.game.deckButtonWidth * 1.8;
		
		
		//ig.game.deckButtonY = ig.game.ty + (ig.system.height * .4) + ig.game.deckButtonHeight;
		ig.game.deckButtonY = ig.game.tb - ig.game.deckButtonHeight * 2;
		ig.game.deckButton1X = ig.game.tWid - (ig.game.deckButtonWidth * 3.66) - 15;
		ig.game.deckButton2X = ig.game.tWid - (ig.game.deckButtonWidth * 2.33) - 15;
		ig.game.deckButton3X = ig.game.tWid - (ig.game.deckButtonWidth * 1) - 15;
		
		this.dFonts.changeFont(ctx, 4);
		ctx.fillStyle = "#33FF33";
		
		if (mySavedDeck1.length > 0 || mySavedDeck2.length > 0 || mySavedDeck3.length > 0){
			ctx.fillText("My Decks", ig.game.tWid - (ig.game.deckButtonWidth * 3.75), ig.game.deckButtonY - ig.game.thinLineHeight * .5);			
		}

		
		if (mySavedDeck1.length > 0){
			if (selectedDeck == 1){
				this.drawButton(ig.game.deckButton1X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "1", 3, false, "#000000");
			}
			else{
				this.drawButton(ig.game.deckButton1X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "1", 3, "#000000", "#33FF33");
			}
		}
		if (mySavedDeck2.length > 0){
			if (selectedDeck == 2){
				this.drawButton(ig.game.deckButton2X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "2", 3, false, "#000000");
			}
			else{
				this.drawButton(ig.game.deckButton2X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "2", 3, "#000000", "#33FF33");
			}
		}
		if (mySavedDeck3.length > 0){
			if (selectedDeck == 3){
				this.drawButton(ig.game.deckButton3X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "3", 3, false, "#000000");
			}
			else{
				this.drawButton(ig.game.deckButton3X, ig.game.deckButtonY, ig.game.deckButtonWidth, ig.game.deckButtonHeight, "3", 3, "#000000", "#33FF33");
			}
		}
		//Deck Builder Button
		this.drawButton(ig.game.deckButton1X, ig.game.deckButtonY + ig.game.deckButtonHeight * 1.15, ig.game.deckButtonWidth * 3.66, ig.game.deckButtonHeight * .66, "Deck Builder", 3, "#000000", "#33FF33");
	},
	drawButton: function(x, y, width, height, txt, txtSize, boxColor, txtColor, borderColor, borderSize){
		var ctx = ig.system.context;
		
		//this.dFonts.changeFont(ctx, 3);
		ctx.fillStyle = "#33FF33";
		
		var myBrdrClr = "#343434";
		var myBrdrSize = 2;
		
		if (borderColor){
			myBrdrClr = borderColor;
		}
		if (borderSize){
			myBrdrSize = borderSize;
		}
		if (boxColor){
			ctx.fillStyle = boxColor;
			this.drawABox(x, x + width, y, y + height, borderSize, myBrdrClr, true, boxColor);
		}
		else{
			this.drawABox(x, x + width, y, y + height, borderSize, myBrdrClr, true, "#33FF33");
		}
		//drawABox: (lx, rx, ty, by, lineWidth, lineColor, fill, fillcolor){
		this.dFonts.changeFont(ctx, txtSize);
		
		ctx.fillStyle = "#343434";
		
		if (txtColor){
			ctx.fillStyle = txtColor;
		}
	
		var myTxtWidth = ctx.measureText(txt).width;
		var myTxtX = x + (width /2) - (myTxtWidth / 2);
		var myTxtY = y + (height / 2) + (ig.game.thinLineHeight / 2);
		
		//Shrink font if it's too big for the box
		if (myTxtWidth > width){
			var smallerTxtSize = txtSize - 1;
			if (smallerTxtSize < 1){
				smallerTxtSize = 1;
			}
			this.dFonts.changeFont(ctx, smallerTxtSize);
			ctx.fillStyle = "#343434";
			//Reset color
			if (txtColor){
				ctx.fillStyle = txtColor;
			}
			myTxtWidth = ctx.measureText(txt).width;
			myTxtX = x + (width /2) - (myTxtWidth / 2);
		}
		
		ctx.fillText(txt, myTxtX, myTxtY);
	},
	announceDraw: function (cardName){
		this.flashMessageTimer.set(7);
		this.drewCardNamed = cardName;
		this.announceCardDraw = true;
	},

	loadDeck: function(){

		selectedCards.length = 0;
		//myNFTNames.forEach(function(o){if (myNFTNames.selected == true) result.push(o);} );
		for (let i = 0; i < myNFTNames.length; i++){
			if ( myNFTNames[i].selected){
				selectedCards.push(myNFTNames[i]);
			}
		}
	},
	flashThisText: function(txt, dur, color, size){
		var ctx = ig.system.context;
		
		this.flashingMessage = true;
		
		color ? this.flashMsgColor = color : this.flashMsgColor = this.color6;
		size ? this.flashMsgSize = size : this.flashMsgSize = 3;
		txt ? this.flashingText = txt : this.flashingText = "You did not enter any text, Donzo.";
		dur ? this.flashingMessageTimer.set(dur) : this.flashingMessageTimer.set(3);
		
		this.flMsgDispSwitch = true;
		this.flashingMessageIntravelTimer.set(this.flMsgOnInt);
	},
	manageTransitionVariables: function(dir){
		//Figure out how to manage these better
		//Clear ending
		if (this.endingScreen && ig.game.endingOver){
			this.endingScreen = false;
			this.flickerTotalCount = 0;
			this.flickerCount = 0;
			this.flickerFreq = 1;
			ig.game.endingOver = false;
			ig.game.gameWon = false;
			this.levelCleared = false;
		}
		//Player is dead.
		if (ig.game.playerDead && !this.managingPlayerDeath ){
			this.managePlayerDeath();
		}
		//Level Clear
		if (this.levelCleared){
			this.levelCleared = false;
		}
	},
	drawTransition: function(){
		var ctx = ig.system.context;
				
		//**************FadeIn*************
		if (this.transitionType == "fadeIn"){
			var curOpacity = 0;
			if (this.transitionTimer.delta() < 0){
				curOpacity = this.transitionTimer.delta() * -1;
			}
			//Prepare Transition for Clear
			if (this.transitionTimer.delta() > 0){
				this.transition = false;
				this.transitionReady = false;
				ig.game.pause = false;
				this.manageTransitionVariables();
			}
			ctx.globalAlpha = curOpacity;
			this.drawABox(0, ig.system.width, 0, ig.system.height, 0, this.slideColor, true, this.fadeColor);
		}
		//*************FadeOut*************
		if (this.transitionType == "fadeOut"){
			var curOpacity = 1;
			if (this.transitionTimer.delta() < 1){
				curOpacity = this.transitionTimer.delta();
			}
			//Level is Ready to Load
			if (this.transitionTimer.delta() > 1){
				this.readyToLoad = true;
				this.manageTransitionVariables();
			}
			//Prepare Transition for Clear
			if (this.transitionTimer.delta() > 2){
				this.transitionReady = true;
				this.transition = false;
			}
			ctx.globalAlpha = curOpacity;
			this.drawABox(0, ig.system.width, 0, ig.system.height, 0, this.slideColor, true, this.fadeColor);
		}
		//***************SlideDownIn*************
		if (this.transitionType == "slideDownIn"){
			
			this.slideAddToY = 0;
			if (this.transitionTimer.delta() < 0){
				this.slideAddToY = this.transitionTimer.delta()  *  ig.system.height;
			}
			//Level is Ready to Load
			if (this.transitionTimer.delta() > 0){
				this.readyToLoad = true;
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
			}
			//Prepare Transition for Clear
			if (this.transitionTimer.delta() > 1){
				this.transitionReady = true;
			}
			
			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0, ig.system.width, 0, ig.system.height + this.slideAddToY, 0, this.slideColor, true, this.slideColor);
		}
		//***************SlideUpIn*************
		if (this.transitionType == "slideUpIn"){
			
			this.slideAddToY = 0;
			if (this.transitionTimer.delta() < 0){
				this.slideAddToY = (this.transitionTimer.delta() *-1)  *  ig.system.height;
			}
			//Level is Ready to Load
			if (this.transitionTimer.delta() > 0){
				this.readyToLoad = true;
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
			}
			//Prepare Transition for Clear
			if (this.transitionTimer.delta() > 1){
				this.transitionReady = true;
			}
			console.log('this.slideAddToY = ' + this.slideAddToY);
			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0, ig.system.width, this.slideAddToY,  ig.system.height + this.slideAddToY ,0, this.slideColor, true, this.slideColor);
		}
		//***************SlideUpOut*************
		if (this.transitionType == "slideUpOut"){
			
			this.slideAddToY = ig.system.height;
			if (this.transitionTimer.delta() < 1){
				this.slideAddToY = this.transitionTimer.delta()  *  ig.system.height;
			}
			else if (this.transitionTimer.delta() < 0){
				this.slideAddToY = 0;
			}
			//Transition is Clear
			if (this.transitionTimer.delta() > 1){
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
				this.transition = false;
				this.transitionReady = false;
				ig.game.pause = false;
				this.manageTransitionVariables();
			}

			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0, ig.system.width, 0, ig.system.height - this.slideAddToY, 0, this.slideColor, true, this.slideColor);
		}
		//***************SlideDownOut*************
		if (this.transitionType == "slideDownOut"){
			
			this.slideAddToY = ig.system.height;
			if (this.transitionTimer.delta() < 1){
				this.slideAddToY = (this.transitionTimer.delta())  *  ig.system.height;
			}
			else if (this.transitionTimer.delta() < 0){
				this.slideAddToY = ig.system.height;
			}
			//Transition is Clear
			if (this.transitionTimer.delta() > 1){
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
				this.transition = false;
				this.transitionReady = false;
				ig.game.pause = false;
				this.manageTransitionVariables();
			}
			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0, ig.system.width, 0 + this.slideAddToY, ig.system.height, 0, this.slideColor, true, this.slideColor);
		}
		//***************SlideRightIn*************
		if (this.transitionType == "slideRightIn"){
			
			this.slideAddToX = 0;
			if (this.transitionTimer.delta() < 0){
				this.slideAddToX = this.transitionTimer.delta()  *  ig.system.width;
			}
			//Level is Ready to Load
			if (this.transitionTimer.delta() > 0){
				//Level is loaded from this transition
				this.readyToLoad = true;
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
			}
			//Prepare Transition for Clear
			if (this.transitionTimer.delta() > 1){
				this.transitionReady = true;
			}
			
			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0, ig.system.width + this.slideAddToX, 0, ig.system.height, 0, this.slideColor, true, this.slideColor);
		}
		//***************SlideRightOut*************
		if (this.transitionType == "slideRightOut"){
			this.slideAddToX = ig.system.width;
			if (this.transitionTimer.delta() < 1){
				this.slideAddToX = (this.transitionTimer.delta())  *  ig.system.width;
			}
			else if (this.transitionTimer.delta() > 1){
				this.slideAddToX = ig.system.width;
			}
			//Transition is Clear
			if (this.transitionTimer.delta() > 1){
				if (ig.Timer.timeScale != 1){
					ig.Timer.timeScale = 1;
				}
				this.transition = false;
				this.transitionReady = false;
				ig.game.pause = false;
				this.manageTransitionVariables();
			}
			//USE SLIDE ADD TO Y to ADD to SCORES OR OTHER TEXT ON DROPS DOWN
			this.drawABox(0 + this.slideAddToX, ig.system.width, 0 , ig.system.height, 0, this.slideColor, true, this.slideColor);
		}
		
		//Restore Alpha
		ctx.globalAlpha = 1;	
	},
	fadeIn: function(delay, color){
		if (!delay){
			ig.game.transitionTimer.set(1);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		ig.game.transitionType = "fadeIn";
		ig.game.transition = true;
		if (color){
			ig.game.fadeColor = color;	
		}
		else{
			ig.game.fadeColor =  this.color3;	
		}
	},
	fadeOut: function(delay, color){
		if (!delay){
			ig.game.transitionTimer.set(0);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}

		ig.game.transitionType = "fadeOut";
		ig.game.transition = true;	
		
		if (color){
			ig.game.fadeColor = color;	
		}
		else{
			ig.game.fadeColor =  this.color3;	
		}
	},
	slideDownIn: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(1);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}
		
		ig.game.transitionType = "slideDownIn";
		ig.game.transition = true;

	},
	slideUpIn: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(1);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}
		ig.game.transitionType = "slideUpIn";
		ig.game.transition = true;
	},
	slideDownOut: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(1);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}
		ig.game.transitionType = "slideDownOut";
		ig.game.transition = true;
	},
	slideUpOut: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(0);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}
		ig.game.transitionType = "slideUpOut";
		ig.game.transition = true;
	},
	//slideRightIn
	slideRightIn: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(1);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}	
		ig.game.transitionType = "slideRightIn";
		ig.game.transition = true;
	},
	//slideRightOut
	slideRightOut: function(delay, color, speed){
		if (!delay){
			ig.game.transitionTimer.set(0);
		}
		else{
			ig.game.transitionTimer.set(delay);	
		}
		if (color){
			this.slideColor = color;
		}
		else{
			this.slideColor = this.color3;
		}
		if (speed){
			if (ig.Timer.timeScale != speed){
				ig.Timer.timeScale = speed;
			}
		}
		else{
			if (ig.Timer.timeScale != 3){
				ig.Timer.timeScale = 3;
			}
		}
		ig.game.transitionType = "slideRightOut";
		ig.game.transition = true;
	},
	
	drawMuteButton: function(){
		var bRight = ig.system.width - 84;
		var bTop = 10;
			
		if (this.muteGame){
			this.buttonMuted.draw(bRight, bTop);
		}
		else{
			this.buttonMute.draw(bRight, bTop);	
		}
	},
	flashScreenBro: function(color, time){
		this.flashScreen = true;
		//If time is provided, set the timer, else go to default time
		if (time){
			this.flashScreenTimer.set(time);
		}
		else{
			this.flashScreenTimer.set(.05);
		}
		this.flashScreenColor = color;
	},
	flashScreenCheck: function(){
		if (this.flashScreen){
			this.drawABox(0, ig.system.width, 0, ig.system.height, 0, this.flashScreenColor, true, this.flashScreenColor);
			//Turn off screen flash if flashtimer hits 0.
			if (this.flashScreenTimer.delta() > 0){
				this.flashScreen = false;
			}
		}
		
	},
	loadGame: function(){
		if (window.localStorage.getItem("gameMuted")){
			this.muteGame = JSON.parse(window.localStorage.getItem("gameMuted"));
		}
	},
	loadTSImages: function(){
		this.tsImage = new Image();
		this.tsImage.src = window.tsImage.src;
		
		this.newGameButton = new Image();
		this.newGameButton.src = window.ngbut.src;
		
		this.continueButton = new Image();
		this.continueButton.src = window.conbut.src;
		
		this.dsImage = new Image();
		this.dsImage.src = window.dsImage.src;
		
		this.menuDownBut = new Image();
		this.menuDownBut.src = window.menuDownBut.src;
		
		this.menuDownButDisabled = new Image();
		this.menuDownButDisabled.src = window.menuDownButDisabled.src;
		
		this.menuUpBut = new Image();
		this.menuUpBut.src = window.menuUpBut.src;
		
		this.menuUpButDisabled = new Image();
		this.menuUpButDisabled.src = window.menuUpButDisabled.src;
	},
	
	resizeYo: function(){

		var theWidthToMeasure = window.innerWidth;
		
		var scale = returnGameScale();
		
		window.scale = scale;
		
		//Set Canvas Width Minus Ads
		this.cWidth = window.innerWidth;
		this.cHeight = window.innerHeight;
		
		//Resize the canvas style and tell Impact to resize the canvas itself;
		canvas.style.width = this.cWidth + 'px';
		canvas.style.height = this.cHeight + 'px';
		
		this.xCamera = 0;
		this.yCamera = 240;
		
		ig.system.resize( this.cWidth * scale, this.cHeight * scale);
		//SET FONTS
		ig.game.dFonts.setVs();
		
	}
	//END ig.game
});



	
	var myScale = returnGameScale();
	
	window.scale = myScale;


	canvas.style.width = window.innerWidth + 'px';
	canvas.style.height = window.innerHeight+ 'px';

	window.addEventListener('resize', function(){

	//If the game hasn't started yet, there's nothing to do here
	if( !ig.system ){ return; }
		if (ig.game){
			ig.game.resizeYo();	
		}
	}, false);

	var width = window.innerWidth * myScale,
	height = window.innerHeight * myScale;
	ig.main( '#canvas', MyGame, 60, width, height, 1 );

});

