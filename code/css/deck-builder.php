<style>
		#deck-builder-buttons{
			background: #D3D3D3;
			border-style: solid;
			border-color: #FF69B4;
			color: #000000;
			display: flex;
			flex-direction: row;
			justify-content: space-around;
			margin: -20px 2em 1em;
			padding: 1em;
		}
		.dbRow{
			display: flex;
		}
		.dbColumn{
			width: 50%;
			border-style: solid;
			padding: 5px;
		}
		.cards{
			cursor:pointer;
			line-height:2em;
		}
		.cardPickSelected{
			background: #FFFFFF;
			color: #000000;
			border-style: solid;
			border-color: #FF69B4;
		}
		#cardDisplayFrame{
			display: none;
			justify-content: center;
			align-items: center
		}
		#cardDisplayBox{
			display: flex;
			justify-content: center;
			align-items: center;
			background: #000000;
			color: #33FF33;
			border-style: solid;
			border-color: #343434;
			border-width: 0.2em;
			padding: .25em 1em;
		}
		#cardDisplay{
			margin: 1em .25em;
			height: 300px;
		}
		#cardDataDisplay{
			padding: .25em 1em;
		}
		#cdName{
			font-size: 2.5em;
			line-height: 2em;
		}
		#cdType{
			font-size: 1.25em;
			line-height: 1.25em;
		}
		#cdLink, #cdLink a:link, #cdLink a:visited, #cdLink a:hover {
			color: #33FF33;
			font-size: 1.05em;
			margin-top:.2em;
			line-height: 1.25em;
		}
		#saved-decks{
			border-color: #33FF33;
			border-style: solid;
			padding: 5px;
			display:none;
		}
		#saved-decks-col{
			display: flex;
			width: 100%;
			justify-content: space-around;
		}
		.sDeckBut{
			font-family: 'Space Mono', monospace;
			padding: 0.5em 1em;
			font-size:1.25em;
			background: #000000;
			border-style: solid;
			border-color: #33FF33;
			color: #33FF33;
			display: flex;
			flex-direction: row;
			justify-content: space-around;
		}
		.sDeckBut:hover, .sDeckButSel{
			background: #33FF33;
			border-color: #FF69B4;
			color: #000000;
		}
		#sync-message{
			margin-bottom: .5em;
			text-align: center;
		}
		.savedDeck{
			border-color: #33FF33;
			border-style: solid;
			padding: 5px 3em;
			width: 100%;
		}/* Skinny Screen */
		@media only screen 
		  and (max-width: 899px) { 
			.terminal .terminalBody {
				font-size:.65em;
			}
			#deck-builder-buttons {
				margin: .5em 1em;
			}
			#cdName {
				font-size: 1.15em;
				line-height: 1.25em;
			}
			#cardDisplay {
  			  height: 120px;
			}
			#cdType {
				font-size: .9em;
				line-height: 1.1em;
			}
			#cdLink{
				font-size: .8em;
				line-height: .9em;
			}

	</style>