<div class="terminal space shadow" id="terminal-div">
				<div class="top">
					<div class="btns">
						<span class="circle red" onclick="tButtonPressed('red',1)"></span>
						<span class="circle yellow" onclick="tButtonPressed('yellow',1)"></span>
						<span class="circle green" onclick="tButtonPressed('green',1)"></span>
					</div>
					<div class="terminal-title">
					 <span class='longWindowTitle'>Happy Fun Kill Club | </span>Home	~	-zsh -- 80x24
					</div>
				</div>
				<div class="terminalBody" id="terminalBody">
				<div class='terminal-text' id='introPara'>
					<!-- Welcome to the Happy Kill Fun Club, Player. -->
				</div>
				<div class='terminal-input' id='terminal1-input'>
					<div id='terminalHistory'></div>
					<form id='terminal-form' action="javascript:void(0);" onsubmit="terminalInput(1)">
						<label id='tLBL' for="t1in"><span id='termUsrName'>player1@hfkc</span> ~ %:</label>
						<input style="display:inline;" type="text" id="t1in" class="tinput" name="t1in" autofocus>
						<input type="hidden" id="walletStatus" name="walletStatus" value="0">
						<input type="hidden" id="walletAddressField" name="walletAddressField" value="false">
						<input type="hidden" id="buyCardsField" name="buyCardsField" value="<?php echo $_GET['buyCards']; ?>">
						<input type="hidden" id="savedDeck" name="savedDeck" value="">
						<input type="hidden" id="savedDeckNum" name="savedDeckNum" value="">
						<input type="hidden" id="deckBuilderField" name="deckBuilderField" value="">
					</form>
				</div>
			</div>
		</div>