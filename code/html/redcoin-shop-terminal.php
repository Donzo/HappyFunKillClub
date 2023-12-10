<?php
	//$item1name = "Level 2 Bulletproof Vest";
	//$item1cost = 10;
	//$item1cardLink = "/cards/items/level-2-bulletproof-vest/card.jpg";
	
	
	//$item2name = "Scroll of Limited Darkness";
	//$item2cost = 25;
	//$item2cardLink = "/cards/items/scroll-of-limited-darkness/card.jpg";
	
	
	//$item3name = "Advanced Assisted Optics";
	//$item3cost = 100;
	//$item3cardLink = "/cards/items/advanced-assisted-optics/card.jpg";
	
	//Next Item
	$item1name = "Lucky Rabbit's Foot";
	$item1cost = 3;
	$item1cardLink = "/cards/items/lucky-rabbits-foot/card.jpg";
	
	//Next Item
	$item2name = "Intermediate First Aid Kit";
	$item2cost = 15;
	$item2cardLink = "/cards/items/intermediate-first-aid-kit/card.jpg";
	
	//Next Item
	$item3name = "Potion of Regeneration";
	$item3cost = 100;
	$item3cardLink = "/cards/items/potion-of-regeneration/card.jpg";
?>
		<div class="terminal space shadow displayNone" id="terminal-div-04">
			<div class="top">
				<div class="btns">
					<span class="circle red" onclick="tButtonPressed('red' , 4)"></span>
					<span class="circle yellow" onclick="tButtonPressed('yellow' , 4)"></span>
					<span class="circle green" onclick="tButtonPressed('green', 4)"></span>
				</div>
				<div class="terminal-title">
					<span class='longWindowTitle'>REDCOIN SHOP</span> -zsh -- 80x24
				</div>
			</div>
			<div class="terminalBody" id="terminalBody-03" style="height:75vh;">
				<h2 class='termH2 rcsh2'>WELCOME TO THE REDCOIN SHOP!</h2>
				<div class="redcoin-shop-terminal-content" id="redcoin-shop-terminal-content">
					<h3 class='rcsh3 center-text '>Check Out These Awesome Wares!</h3>
					<div class='flexBreak'></div>
					<div id='purchase-progress-div'></div>
					<div class='flexBreak'></div>
					<div class='rcs-item' id='rcs-item-01'>
						<div class='rcs-card-img'>
							<a href='<?php echo $item1cardLink ?>' target='_blank'>
								<img src="<?php echo $item1cardLink ?>"/>
							</a>
						</div>
						<div class='rcs-desc-unit'>
							<div class='rcs-item-title'>
								<?php echo $item1name; ?>
							</div>
							<div class='rcs-item-cost'>
								MINT COST: <?php echo $item1cost; ?> RedCoins
							</div>
							<div class='rcs-buy-button-div'>
								<button class='rcs-buy-button' onclick='buyItem(1, <?php echo $item1cost; ?>)'>Buy Now</button>
							</div>
						</div>
					</div>
					<div class='flexBreak'></div>
					<div class='rcs-item' id='rcs-item-02'>
						<div class='rcs-card-img'>
							<a href='<?php echo $item2cardLink; ?>' target='_blank'>
							<img src="<?php echo $item2cardLink; ?>"/>
							</a>
						</div>
						<div class='rcs-desc-unit'>
							<div class='rcs-item-title'>
								<?php echo $item2name ?>
							</div>
							<div class='rcs-item-cost'>
								MINT COST: <?php echo $item2cost; ?> RedCoins
							</div>
							<div class='rcs-buy-button-div'>
								<button class='rcs-buy-button' onclick='buyItem(2, <?php echo $item2cost; ?> )'>Buy Now</button>
							</div>
						</div>
					</div>
					<div class='flexBreak'></div>
					<div class='rcs-item' id='rcs-item-03'>
						<div class='rcs-card-img'>
							<a href='<?php echo $item3cardLink; ?>' target='_blank'>
								<img src="<?php echo $item3cardLink; ?>"/>
							</a>
						</div>
						<div class='rcs-desc-unit'>
							<div class='rcs-item-title'>
								<?php echo $item3name; ?>
							</div>
							<div class='rcs-item-cost'>
								MINT COST: <?php echo $item3cost; ?> RedCoins
							</div>
							<div class='rcs-buy-button-div'>
								<button class='rcs-buy-button' onclick='buyItem(3, <?php echo $item3cost; ?>)'>Buy Now</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>