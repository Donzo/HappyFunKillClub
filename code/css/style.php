<style>
 		body {
  			background-color: #FF69B4;
  			color: #FFFFFF;
  			font-family: 'Space Mono', monospace;
  			margin: 0;
			padding: 0;
  			line-height: 1em;
  			font-size:.8em;
		}
		img {
			max-width: 100%;
			max-height: 100%;
			height: auto;
		}
		#header{
			height:200px;
			background-color: #2E2160;
			color: #FFD700;
			display: flex;
			justify-content: center;
		}
		#header-image img{
			height: 100%;
		}
		#content-frame{
			background-color: #FF69B4;
			padding: 0 2em;
			display: flex;
			justify-content: center;
		}
		#content{
			width: 100%;
			height: auto;
			margin: 0 2em;
			padding: .5em 2em;
			background-color: #C0C0C0;
		}
		.para{
			margin: .5em 1em;
			font-size:1.5em;
		}
		.button{
  			background-color: #2E2160;
			border: none;
			color: #FFD700;
			padding: 15px 32px;
			text-align: center;
			text-decoration: none;
			display: inline-block;
			float: left;
			font-size: 16px;
			margin: 1em;
  		}
  		#tos-purchase-div, #deck-builder-div{
  			display:none;
  		}
  		
		
		
  		/* Skinny Screen */
		@media only screen 
		  and (max-width: 899px) { 
			#content-frame{
				padding: 0;
			}
			#content{
				margin: 5px;
				padding: 5px;;
			}
			#header {
		  		height: 100px;
		  	}
			.longWindowTitle{
				display:none;
			}
			.button {
			    padding: 10px 14px;
			    font-size: 12px;
    			margin: 1em .4em;
			}
	</style>