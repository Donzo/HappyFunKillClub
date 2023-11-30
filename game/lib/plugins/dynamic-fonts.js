ig.module( 
    'plugins.dynamic-fonts' 
)
.requires(
    'impact.impact'
)
.defines(function(){

DynamicFonts = ig.Class.extend({ 
	
	defaultFontColor: "#000000",
	defaultFontName: "Space Mono",
	dynamicFontSize: null,
	dynamicFontSize2: null,
	dynamicFontSize3: null,
	dynamicFontSize4: null,
	dynamicFontSize5: null,
	dynamicFontSize6: null,
	dynamicFontSize7: null,
	dynamicFontSizeHeader: null,
	dynamicFontSizeHeaderTLH: null,

	
	vh: null,
	vw: null,
	vmin: null,
	vmax: null,
	fontSizeY: null,
	cursorPosX: null,
	cursorPosY: null,
	cursorPosYNewLine: null,
	lastLineHeight: null,
	//Don't forget to reset known line size variable after using it
	headerSizeKnown: false,

	
	ac1LSN: false,
	ac2LSN: false,
	ac3LSN: false,
	ac4LSN: false,
	clHeightKnown: false,
	dynamicLineHeightHeader: null,
	dynamicLineHeightHeaderTLH: null,
	shrinkFactor1: .75,
	shrinkFactor2: .9,
	shrinkFactor3: .8,
	
	init: function( ) {
		this.setVs();
	},
	dynamicFontChange: function(context, style, size, unit, fillColor){
		
		if (window.scale > .8){
			if (window.innerHeight < window.innerWidth){
				size *= 1 + window.scale;
			}
			else{
				size *= 1 + (window.scale / 3);
			}
		}
		
		this.dfsPx = size;
		//Header font
		if (style == "header"){
			context.font =  size + 'px ' + this.defaultFontName; 
			this.dynamicFontSizeHeader = size + 'px ' + this.defaultFontName; 
			//Set a smaller font size for two line headers
			this.dynamicFontSizeTLH = (size * this.shrinkFactor1) + 'px ' + this.defaultFontName; 
		}
		//Color
		if (fillColor){
			ig.system.context.fillStyle = fillColor;
		}
		else{
			ig.system.context.fillStyle = this.defaultFontColor;
		}
	},
	//We need to set viewport units for dynamic font setting
	setVs: function(){
		this.vh = document.documentElement.clientHeight * .01;
		this.vw = document.documentElement.clientWidth * .01;
		if (document.documentElement.clientWidth > document.documentElement.clientHeight){
			this.vmin = this.vh ;
			this.vmax = this.vw;
		}
		else{
			this.vmax = this.vh ;
			this.vmin = this.vw;
		}
	},
	getVminFontSizeY: function(vMin){
		vMin *= .01;
		//Portrait
		if (document.documentElement.clientHeight > document.documentElement.clientWidth){
			this.fontSizeY = document.documentElement.clientWidth * vMin;
		}
		//landscape
		else{
			this.fontSizeY = document.documentElement.clientHeight * vMin;	
		}
	},
	changeFont: function(context, style, alteration){
		
		if (style == 1){
			var vmin1 = this.vmin * 1;
			context.font= vmin1 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(1);
			this.style1LineHeight  = (vmin1 * 1.05);
			ig.game.curLineHeight = this.style1LineHeight;
		}
		//HUD Font
		else if (style == 2){
			var vmin2 = this.vmin * 2;
			context.font= vmin2 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(2);
			this.style2LineHeight  = (vmin2 * 1.05);
			ig.game.curLineHeight = this.style2LineHeight;
		}
		else if (style == 3){
			var vmin3 = this.vmin * 3;
			context.font= vmin3 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(3);
			this.style3LineHeight  = (vmin3 * 1.05);
			ig.game.curLineHeight = this.style3LineHeight;
		}
		else if (style == 4){
			var vmin4 = this.vmin * 4;
			context.font= vmin4 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(4);
			//Set Line Height
			this.style4LineHeight  = (vmin4 * 1.05);
			ig.game.curLineHeight = this.style4LineHeight;
		}
		else if (style == 5){
			var vmin5 = this.vmin * 5;
			context.font= vmin5 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(5);
			//Set Line Height
			this.style5LineHeight  = (vmin5 * 1.05);
			ig.game.curLineHeight = this.style5LineHeight;
		}
		else if (style == 6){
			var vmin6 = this.vmin * 6;
			context.font= vmin6 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(6);
			this.style6LineHeight  = (vmin6 * 1.05);
			ig.game.curLineHeight = this.style6LineHeight;
		}
		else if (style == 7){
			var vmin7 = this.vmin * 7;
			context.font= vmin7 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(7);
			this.style7LineHeight  = (vmin7 * 1.05);
			ig.game.curLineHeight = this.style7LineHeight;
		}
		else if (style == 8){
			var vmin8 = this.vmin * 8;
			context.font= vmin8 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(8);
			this.style8LineHeight  = (vmin8 * 1.05);
			ig.game.curLineHeight = this.style8LineHeight;
		}
		else if (style == 9){
			var vmin9 = this.vmin * 9;
			context.font= vmin9 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(9);
			this.style9LineHeight  = (vmin9 * 1.05);
			ig.game.curLineHeight = this.style9LineHeight;
		}
		else if (style == 10){
			var vmin10 = this.vmin * 10;
			context.font= vmin10 + 'px ' + this.defaultFontName;
			ig.system.context.fillStyle = this.defaultFontColor;
			this.getVminFontSizeY(10);
			this.style10LineHeight  = (vmin10 * 1.05);
			ig.game.curLineHeight = this.style10LineHeight;
		}
		ig.game.thinLineHeight = this.fontSizeY * .86;
		ig.game.vThinLineHeight = this.fontSizeY * .76;
		ig.game.evThinLineHeight = this.fontSizeY * .66;
	},
    wrapTheText: function(context, text, x, y, maxWidth, lineHeight) {
		// Return if no text is provided.
		if (!text) {
			console.log('no txt...');
			context.fillText("null", x, y);
			return;
		}

		// Adjust line height based on window scale and orientation.
		if (window.scale > .8) {
			lineHeight *= window.innerHeight < window.innerWidth ? 
			(1 + window.scale) : 
			(1 + (window.scale / 3));
		}

		// Split the text into words.
		const words = text.split(" ");
		let line = "";

		for (const word of words) {
			const testLine = line + word + " ";
			const testWidth = context.measureText(testLine).width;

			if (testWidth > maxWidth) {
				context.fillText(line, x, y);
				line = word + " ";
				y += lineHeight;
       		} 
        	else{
				line = testLine;
			}
		}

		// Draw the last line.
		context.fillText(line, x, y);

		// Update cursor and other properties.
		this.cursorPosX = x;
		this.cursorPosY = y;
		this.cursorPosYNewLine = y + lineHeight;
		this.lastLineHeight = lineHeight;
	},
	setTxtSizeHUD: function(context, size){
		//Set Fontsize
		var sizeCalc  = (ig.system.width * .05 ) * size;
		//Store Line Height
		ig.game.lineHeightHUD = sizeCalc * 1.15;
		//Change Font
		context.font=  sizeCalc + 'px ' + this.defaultFontName; 
		//Store Font
		this.fontSizeHUD = sizeCalc + 'px ' + this.defaultFontName; 	
	},
	calcLineSize: function(context, text, x, y, maxWidth, maxNumberOfLines) {
		if (text != null){
			//Initalize default sizes and increments
			var defaultFontSize = this.vh * 5;
			var increment =  this.vh  * .1;
			var lineHeight = defaultFontSize * 1.125;
			var lineCount = null;
			//////The do...while statement creates a loop that executes a specified statement until the test condition evaluates to false.
			do {
				lineCount = 0;
				var words = text.split(" ");
				var line = "";
				//Set Header Size
				if (!this.headerSizeKnown && ig.game.qHead){
					this.dynamicFontChange(context, "header", defaultFontSize, "px");
				}
				

				for(var n = 0; n < words.length; n++) {
					var testLine = line + words[n] + " ";
					var metrics = context.measureText(testLine);
					var testWidth = metrics.width;

					if(testWidth > maxWidth) {					
						line = words[n] + " ";
						lineCount++;
					}
					else {
						line = testLine;
					}
				}
				//We have more lines than allowed or we have exceeded the allotted Y space
				if (lineCount >= maxNumberOfLines){
					//Lower the default font size by increment
					defaultFontSize = defaultFontSize- increment;
					//update line height
					lineHeight = defaultFontSize * 1.1;
					//initialize words again
					words.length = 0;
					var words = text.split(" ");
				}
			}
			//Once this statement is false, loop will end.
			while (lineCount >= maxNumberOfLines);
			
			//Run That Same Shit and Check Against Total Height
			//////The do...while statement creates a loop that executes a specified statement until the test condition evaluates to false.
			do {
				lineCount = 0;
				var words = text.split(" ");
				var line = "";
				//Make Sure Header Size Fits Within Alloted Space
				var allotedSpaceY = null;
				if (!this.headerSizeKnown && ig.game.qHead){
					this.dynamicFontChange(context, "header", defaultFontSize, "px");
					//This is the header. Set Y Max to Max Header Height
					allotedSpaceY =  ig.game.maxHeaderHeight;
				}
				
				for(var n = 0; n < words.length; n++) {
					var testLine = line + words[n] + " ";
					var metrics = context.measureText(testLine);
					var testWidth = metrics.width;

					if(testWidth > maxWidth) {					
						line = words[n] + " ";
						lineCount++;
					}
					else {
						line = testLine;
					}
				}
				//If we exceed the space allotment, shrink the font.
				if ( lineHeight * lineCount  > allotedSpaceY){
					//Lower the default font size by increment
					defaultFontSize = defaultFontSize- increment;
					//update line height
					lineHeight = defaultFontSize * 1.1;
					//initialize words again
					words.length = 0;
					var words = text.split(" ");
				}
				
			}
			//Once this statement is false, loop will end.
			while (lineHeight * lineCount  > allotedSpaceY);
			
			//We can proceed now that the conditions have been satisfied.
			
			//The Sizes are Now Known
			if (!this.headerSizeKnown){
				this.headerSizeKnown = true;
				this.headerLineCount = lineCount + 1;
				//Update Line Height - Make it Smaller if We Need To
				this.dynamicLineHeightHeader = lineHeight;
				this.dynamicLineHeightHeaderTLH = lineHeight * this.shrinkFactor1;
				
			}
			
		}	
		else{
			console.log('you are sending a NULL to the line counter.');	
		}
	},
});
});