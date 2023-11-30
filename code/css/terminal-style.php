<style>
  		.terminal{
			background-color: #000000;
			color: #33FF33;
			width: 100%;
		}
  		/* Terminal Styling */
  		#terminal-div{
  			visibility: visible;
  		}
		.terminal {
    		border-radius: 5px 5px 0 0;
			position: relative;
			width: 95%;
		}
		.terminal .top {
			background: #343434;
			color: #CCCCCC;
			padding: 7px;
			border-radius: 5px 5px 0 0;
		}
		.terminal .btns {
			position: absolute;
			top: 7px;
			left: 5px;
		}
		.terminal .circle {
			width: 12px;
			height: 12px;
			display: inline-block;
			border-radius: 15px;
			margin-left: 2px;
			border-width: 1px;
			border-style: solid;
		}
		.terminal-title{
			text-align: center;
		}
		.red { background: #EC6A5F; border-color: #D04E42; }
		.green { background: #64CC57; border-color: #4EA73B; }
		.yellow{ background: #F5C04F; border-color: #D6A13D; }
		.clear{clear: both;}
		.terminal .terminalBody {
			background: black;
			color: #7AFB4C;
			padding: 2em 2em;
			overflow: auto;
		}
		.terminalBody{
			height:33vh;
			overflow-x: hidden;
			overflow-y: scroll;
		}
		.terminalBodyExpanded{
			height: calc(80vh - 200px);
		}
		.terminalBody::-webkit-scrollbar{
			background: #000000;
		}
		.space {
			margin: 25px;
		}
		.shadow { box-shadow: 0px 0px 10px rgba(0,0,0,.4)}
		#terminal-form{
			display: flex;
		}
		.terminal-text{
			margin: 0;
			padding: 0;
			text-align: left;
			min-height:10vh;
		}
		#tLBL{
			margin: 5px;
		}
		.tinput{
			background-color: #000000;
			color: #33FF33;
			width: 70%;
			border: 0;
			flex: 2;
			align-items: baseline;
			font-family: 'Space Mono', monospace;
		}
		#terminalHistory{
			line-height: 2em;
			margin: 5px 5px 0 5px;
		}
		input.tinput:focus{
			outline: none;
		}
		.terminal-programs{
			color: #0000FF;
		}
		.terminal-dir{
			color: #FFFFFF;
		}
		/* Skinny Screen */
		@media only screen 
		  and (max-width: 899px) { 
			#content {
    			padding: 0;
    			margin: 0;
    		}
			.terminal .terminalBody {
				padding: 2em .5em;
			}
			.space {
  				margin: 10px;
			}
		}
	</style>