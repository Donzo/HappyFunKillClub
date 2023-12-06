<div id="deck-builder-div">
		<div class="terminal space shadow" id="terminal-div-02">
				<div class="top">
					<div class="btns">
						<span class="circle red" onclick="tButtonPressed('red' , 2)"></span>
						<span class="circle yellow" onclick="tButtonPressed('yellow' , 2)"></span>
						<span class="circle green" onclick="tButtonPressed('green', 2)"></span>
					</div>
					<div class="terminal-title">
					<span class="longWindowTitle">DECK BUILDER</span> -zsh -- 80x24
					</div>
				</div>
				<div class="terminalBody" id="terminalBody-02">
					<h2 class="termH2">DECK BUILDER</h2>
					<div id="sync-message"></div>
					<div id='loading-wheel-div-02' class='hide'>
						<img src='/images/loading-wheel-02.gif'/>
					</div>
					<div class="dbRow" id="saved-decks">
						<div id="saved-decks-col">
							<h2 class="termH2">SAVED DECKS</h3>
							<button class="sDeckBut" id="sDeckBut1" onclick="selectDeck(1, this.id)">Deck 1</button>
							<button class="sDeckBut" id="sDeckBut2" onclick="selectDeck(2, this.id)">Deck 2</button>
							<button class="sDeckBut" id="sDeckBut3" onclick="selectDeck(3, this.id)">Deck 3</button>
							<button class="sDeckBut sDeckButSel" id="sDeckBut4" onclick="selectDeck(4, this.id)">Make a New Deck</button>
						</div>
					</div>
					
					<div class="dbRow" id="db-main-row">
						<div class="dbColumn">
							<h2 class="termH2">MY CARDS</h2>
							<div class="cards" id="character-cards">
								<h3 class="termH3">Character Cards</h3>
								<ul id="character-cards-list" class="card-list">
									<li id="lic1">Card 1</li>
									<li id="lic2">Card 2</li>
									<li id="lic3">Card 3</li>
								</ul>
							</div>
						</div>
						<div class="dbColumn">
							<h2 class="termH2"><span id='deckBuilderHdrTxt'>MY NEW DECK</span></h2>
							<div class="cards" id="character-cards">
								<h3 class="termH3">Character Cards (<span id="ccSelectedH3">0</span> / 3)</h3>
								<ul id="character-cards-indeck" class="card-list">
									<li id="cc1">You must ADD at least 1 CHARACTER CARD to make a DECK.</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
	<div id="deck-builder-buttons" class="shadow">
		<div class="db-button">
			<button id="addToDeck" onclick="addToDeck()" disabled>Add To Deck</button>
		</div>
		<div class="db-button">
			<button id="dumpDeck" onclick="dumpDeck()" disabled>Dump Deck</button>
		</div>
		<div class="db-button">
			<button id="saveDeck" onclick="saveDeck()" disabled>Save Deck</button>
		</div>
		<div class="db-button">
			<button id="saveDeck" onclick="synchronizeMyDeck()">Synchronize Purchased Cards</button>
		</div>
	</div>
	<div id="cardDisplayFrame">
		<div id="cardDisplayBox" class="shadow">
			<div id="cardDisplay" class="shadow">
	
			</div>
			<div id="cardDataDisplay">
				<div id="cdName"></div>
				<div id="cdType"></div>
				<div id="cdLink"></div>
			</div>
		</div>
	</div>
</div>
	