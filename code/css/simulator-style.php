<style>
 		body {
  			background-color: black;
  			color: yellow;
  			font-family: 'Space Mono', monospace;
  			margin: .5em;
  			padding: .5em;
  			line-height: 1em;
  			font-size:.8em;
		}
		img {
			max-width: 100%;
			height: auto;
		}
		h1 {
			text-align: center;
		}
		.row {
			display: flex;
			flex-direction: row;
			flex-wrap: wrap;
			width: 100%;
			justify-content:center;
		}
		.column, .column-full {
  			display: flex;
  			flex-direction: column;
  			flex-basis: 100%;
  			flex: 1;
  			border: 3px solid yellow;
  			padding: 1em;
  			margin: 1em;
  			font-size: .75em;
  			max-width: 45%;
  		}
  		.column-full{
  			max-width: 90%;
  		}
  		.field, .stat-field{
  			border: 3px solid yellow;
  			padding: 1em;
  			margin: 1em;
  		}
  		.field-lbl{
  			margin: 1.25em 1.25em .25em;
  		}
  		.stat-fields{
  			display: inline-flex;
  		}
  		.stat-field{
  			width: 50%;
  		}
  		.stat-field-small{
  			width: 33.33%;
  		}
  		.stat-lbl{
  			font-size:2em;
  		}
  		.highlight{
  			background-color: yellow;
  			color: black;
  		}
  		input, select, textarea {
			font-family: 'Press Start 2P', cursive;
			font-size: 2em;
			padding: 1em;
		}

  		input[type='radio'] { 
			transform: scale(2); 
      		margin: 2em 2em;
 		}
 		
 		.unit-field, climate-field{
 			margin: 1em;
  			padding: 1em;
 		}
  		.posture-buttons{

  		}
  		.button{
  			background-color: yellow;
			border: none;
			color: black;
			padding: 15px 32px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			float: left;
			font-size: 16px;
			margin: 1em;
  		}
  		.presetButton{
  			max-width: 10%;
  			display: inline;
  		}
  		#run-results{
  			margin: 1em;
  			padding: 1em;
  		}
  		.report-field{
  			margin: .25em;
  			margin-top: 5em;
  			padding: 1em;
  		}
  		.small-text{
  			font-size: .6em;
  		}
  		.cardDisplayWrapper{
  			display:flex;
  			justify-content: center;
  		}
  		.cardDisplay{
  			max-width:50%;
  			margin:2em;
  			padding:2em;
  		}
  		.myDeckView{
  			font-size: 2em;
  			line-height: 1em;	
  		}
	</style>