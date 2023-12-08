<div id='tos-purchase-div'>
		<div class="terminal space shadow" id="terminal-div-03">
				<div class="top">
					<div class="btns">
						<span class="circle red" onclick="tButtonPressed('red' , 3)"></span>
						<span class="circle yellow" onclick="tButtonPressed('yellow' , 3)"></span>
						<span class="circle green" onclick="tButtonPressed('green', 3)"></span>
					</div>
					<div class="terminal-title">
					<span class='longWindowTitle'>TERMS OF SERVICE</span> -zsh -- 80x24
					</div>
				</div>
				<div class="terminalBody" id="terminalBody-03">
					<h2 class='termH2'>Terms of Service for Happy Fun Kill Club NFT Pack Sale</h2>
					<div class="terms-content" id="terms-content">
						<h3 class='termH3'>1. Ownership and Sale of NFT Cards:</h3>
							<p>a. The NFT cards ("Cards") and packs ("Packs") being sold in the sale ("Sale") represent virtual items within the Game.</p>
							<p>b. Each Pack purchased during the Sale grants you a non-exclusive right to exchange the Pack for Cards to use within the Game.</p>
							<p>c. Ownership of the Packs is transferred to the buyer upon successful purchase and payment.</p>
							<p>d. Ownership of the Cards is transferred to the buyer upon successful conversion of a Pack token.</p>
						<h3 class='termH3'>2. Sale Process:</h3>
							<p>a. The Sale will be conducted through a designated platform or marketplace approved by the Game's developer.</p>
							<p>b. The availability, quantity, and pricing of the Cards will be determined by the Game's developer, subject to change without notice.</p>
							<p>c. Participation in the Sale may require the creation of an account on the designated platform.</p>
							<p>d. The purchase of Packs during the Sale is subject to availability and additional terms and conditions imposed by the platform, blockchain, or marketplace hosting the Sale.</p>
							<p>e. The buyer agrees to be solely responsible for any fees, including gas or network fees imposed by the platform, blockchain, or marketplace hosting the Sale. The buyer shall cover 100% of all fees, even in the case of failed sales transactions..</p>
							<p>f. Opening or converting Packs into Cards requires separate transactions. The buyer agrees to pay ALL network fees required for such conversions. The Game's developer shall not be held liable for any fees imposed by the platform, blockchain, or marketplace during the conversion process. The buyer shall cover 100% of all fees for converting Packs into Cards, even in the case of failed or reverted transactions.</p>	
						<h3 class='termH3'>3. Payment and Refunds:</h3>
							<p>a. Payment for the purchased Cards must be made using the designated payment methods accepted during the Sale.</p>
							<p>b. All payments are final and non-refundable, except as required by applicable law or at the sole discretion of the Game's developer.</p>
							<p>c. The Game's developer reserves the right to cancel or modify the Sale, including Card availability, pricing, or any other Sale details, at any time without liability.</p>
						<h3 class='termH3'>4. License and Restrictions:</h3>
							<p>a. The Cards are licensed to you for personal, non-commercial use within the Game.</p>
							<p>b. Any unauthorized use, reproduction, or distribution of the Cards may result in immediate termination of your access to the Game.</p>
						<h3 class='termH3'>5. Intellectual Property:</h3>
							<p>a. The Game, including its characters, artwork, and other related content, is protected by copyright and other intellectual property laws.</p>
							<p>b. You acknowledge and agree that all intellectual property rights in the Game and the Cards are owned by the Game's developer.</p>
							<p>c. You may not use, reproduce, or modify the Game or the Cards, in whole or in part, without prior written permission from the Game's developer.</p>	
						<h3 class='termH3'>6. Disclaimers and Limitation of Liability:</h3>
 							<p>a. The Game's developer provides the Cards on an "as is" basis, and makes no warranties or guarantees, express or implied, regarding their functionality, availability, or performance.</p>
							<p>b. To the fullest extent permitted by law, the Game's developer shall not be liable for any direct, indirect, incidental, consequential, or special damages, including but not limited to, damages for loss of profits, data, or other intangible losses, arising out of or in connection with the purchase, use, or inability to use the Cards or the Game.</p>
							<p>c. While the Game's developer will make reasonable efforts to maintain the use of Cards and Packs for a period of 10 years after the start of the Sale, the Game's developer does not guarantee uninterrupted access or availability. The Game's developer shall not be held liable for any interruptions, disruptions, or downtime that may occur.</p>
						<h3 class='termH3'>7. Modifications to Terms:</h3>
							<p>a. The Game's developer reserves the right to modify or update these Terms of Service at any time without notice.</p>
							<p>b. Your continued participation in the Sale or use of the Cards after any modifications to the Agreement constitutes your acceptance of the revised terms.</p>
						<h3 class='termH3'>8. Governing Law and Jurisdiction:</h3>
							<p>a. This Agreement shall be governed by and construed in accordance with the laws of the jurisdiction where the Game's developer is located.</p>
							<p>b. Any disputes arising out of or in connection with this Agreement shall be subject to the exclusive jurisdiction of the courts in the aforementioned jurisdiction.</p>
							<p>By participating in the Sale, you acknowledge that you have read, understood, and agreed to be bound by these Terms of Service. If you do not agree with any provision of this Agreement, you should refrain from participating in the Sale or using the Cards.</p>
					</div>
				</div>
			</div>
	<div id='agree-to-terms-box' class='shadow'>
		<span id="checkboxWrapper">
			<input type="checkbox" id="tosCheckBox" onchange="handleCheckboxChange()" disabled="disabled"/>
			<div id="checkboxOverlay" onclick="scrollTOU()"></div>
		</span>

		<label for="tosCheckBox"><span id='tos-agree'> &nbsp; I have read, understood, and agreed to the Terms of Service for the Happy Fun Kill Club NFT Pack Sale.</span></label>
	</div>
	<div id='purchase-packs-slider-unit' class='hide'>
		<!-- Amount to Buy Text -->
		<div class='center-text amountToBuyText'>Buy <span id='amountOfPacks'>1</span> <span id='congegateNounPack'>Pack</span> for <span id='costOfPacks'>.1</span> AVAX.</div>
		<!-- Amount to Slider -->
		<div class="slidecontainer">
			1 Pack &nbsp; <input type="range" min="1" max="10" value="1" class="slider" id="slidePacksToBuy" oninput="adjustPackAmount();"> &nbsp; 10 Packs
		</div>
		<div class='center-text'>Adjust this slider to buy more packs.</div>
		<div id='loading-wheel-div' class='hide'>
			<div>
				<img src='/images/loading-wheel-02.gif'/>
			</div>
			<div id='waiting-msg'>
				Buying Now...
			</div>
		</div>
	</div>
	<div id='confirmation-message' class='hide'>
		Thank you! Your purchase is complete.
	</div>
	
	<div id='buy-button-div'>
		<button id='buy-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO BUY PACK</button>
	</div>
	<div id='users-packs-message-wrapper' class="displayNone">
		<div id='users-packs-message' class='center-text users-packs-message'>
		
		</div>
		<div id='loading-wheel-div-03-wrapper'>

		</div>
		<div id="pulled-cards-div" class="my-cards">
		</div>
	</div>
	
	<div id='open-packs-wrapper'>
		<div id='open-packs-div'>
			<button id='open-pack-button' class='disabledbutton' disabled>AGREE TO TOS TO OPEN PACK</button>
		</div>
	</div>
	<div id="play-game-button-div" class='hide'>
		<button id='play-game-button' class='button' onclick = 'playGame()'>Play the Game Now</button>
	</div>
</div>