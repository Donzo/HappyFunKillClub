<style>
		.card-images-container {
			display: flex;
			flex-wrap: wrap;
			padding: 5px;
			justify-content: space-around;
			margin-top:2em;
		}
		.card-img {
			margin: 5px;
			max-width: 100%;
		}
		.card-link{
			display: inline;
			text-decoration: none;
			max-width: 10%;
		}
		#terms-of-service{
			background-color: #000000;
			color: #FFD700;
			border: 1px solid #C72C27;
			max-height: 400px; /* Adjust the height as needed */
			overflow: auto;
			padding: 2em;
			margin-bottom: 2em;
		}
		.terms-content {
			max-height: 100%;
		}
		.termH2, .center-text{
			text-align: center;
		}
		.termH3{
			display: inline;
		}
		#buy-button-div, #open-packs-div{
			margin-bottom: 1em;
			padding-bottom: 1em;
			text-align: center;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		#open-packs-div{
			margin-bottom: 5em;
		}
		.amountToBuyText{
			font-size: 1.5em;
			margin-bottom: .5em;
			color: #FFD700;
		}
		#users-packs-message-wrapper{
			color: #FF6600;
			background: #000000;
			border-style: solid;
			border:#343434;
			border-width: medium;
			margin: 1em;
			padding: 2em;
		}
		#users-packs-message{
			color: #33FF33;
			font-size: 2em;
			line-height: 2em;
		}
		#waiting-msg{
			color: #33FF33;
		}
		
		.sales-copy{
			margin-top: 2em;
			padding-top: 2em;
			line-height: 3em;
		}
		.slidecontainer {
			width: 100%;
			display: flex;
			align-items: center;
			justify-content: center;
		}
		.slider {
			  -webkit-appearance: none;
			width: 50%;
			height: 25px;
			background: #d3d3d3;
			outline: none;
			opacity: 0.7;
			  -webkit-transition: .2s;
			transition: opacity .2s;
		}

		.slider:hover {
			opacity: 1;
		}

		.slider::-webkit-slider-thumb {
			  -webkit-appearance: none;
			appearance: none;
			width: 25px;
			height: 25px;
			background: #04AA6D;
  			cursor: pointer;
		}

		.slider::-moz-range-thumb {
			width: 25px;
			height: 25px;
			background: #04AA6D;
 			cursor: pointer;
		}
		#purchase-packs-slider-unit{
			margin: 1em;
			padding: 2em;
			background: #000000;
			margin-top: 4em;
		}
		
		#loading-wheel-div, #loading-wheel-div-02, #loading-wheel-div-03{
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
			font-size:2em;
			margin:1em;
		}
		.hide{
			display: none !important;
		}
		#confirmation-message{
			text-align: center;
			font-size:2.2em;
			margin:2em;
		}
		input[type=checkbox]{
			transform: scale(2);
			padding: 10px;
		}
		#tos-agree{
			font-size: 1.5em;
			line-height: 1.2em;
			color: #36454F;
		}
		#pack-balance-div{
			margin:1em;
			background: #87CEEB;
			font-size: 2.2em;
			line-height: 1.5em;
			padding: 1em;
			color: #C72C27;
		}
		#pulled-cards-div{
			display: flex;
			flex-wrap: wrap;
			padding: 5px;
			justify-content: space-around;
			margin-top:2em;
		}
		.my-card-img{
			max-width: 100%;
			display: inline-flex;
			padding-bottom: 2em;
		}
		.flexLink{
			max-width: 20%;
			display: inline-flex;
		}
		.flexBreak {
			flex-basis: 100%;
			height: 0;
		}
		#just-pulled-hdr{
			margin-bottom: 2em;
			font-size: 3em;
			line-height: 1.5em;
			color:#33FF33;
		}
		#play-game-button-div{
			display: flex;
			flex-direction: column;
			align-items: center;
			justify-content: center;
		}
		#tosCheckBoxDiv{
			display: inline;
		}
		#checkboxWrapper {
			position: relative;
		}
		#checkboxOverlay {
			position: absolute;
			left: 0;
			right: 0;
			top: 0;
			bottom: 0;
		}
		#agree-to-terms-box{
			background: #000000;
			color: #FF0000;
			margin: 1em 2em 1em 2em;
			padding: 2em;
			width: 90%;
			line-height: 2.5em;
		}
		/* Skinny Screen */
		@media only screen 
		  and (max-width: 899px) { 
			#agree-to-terms-box{
				margin: 1em .5em 1em .5em;
				padding: 2em;
				width: inherit;
				line-height: 2.5em;
			}
			#tos-agree {
				font-size: 1.1em;
				line-height: 1.5em;
			}
			
		}
	</style>