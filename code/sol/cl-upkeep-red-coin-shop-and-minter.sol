// SPDX-License-Identifier: MIT
pragma solidity ^0.8.9;

import "@openzeppelin/contracts@4.9.0/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts@4.9.0/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts@4.9.0/token/ERC721/extensions/ERC721Enumerable.sol";
import "@openzeppelin/contracts@4.9.0/access/Ownable.sol";


interface RedCoin {
	function transferFrom(address, address, uint) external returns (bool); //from to amount
	function transfer(address, uint256) external returns (bool);
	function allowance(address, address) external returns (uint256); //owner, spender
	function balanceOf(address) external returns (uint256); 
}


//Character Card Contract
contract HappyFunKillClubItemMinter is ERC721, ERC721Enumerable, ERC721URIStorage, Ownable {
	
	uint256 public nextTokenId;
	uint256 public redCoinAllowance;

	uint256 public item1Cost;
	uint256 public item2Cost;
	uint256 public item3Cost;

	uint256 public nextItem1Cost;
	uint256 public nextItem2Cost;
	uint256 public nextItem3Cost;

	address public CLUpkeepAddress;

	string item1URI = "https://happyfunkillclub.com/cards/items/level-2-bulletproof-vest/nft.json";
	string item2URI = "https://happyfunkillclub.com/cards/items/scroll-of-limited-darkness/nft.json";
	string item3URI = "https://happyfunkillclub.com/cards/items/advanced-assisted-optics/nft.json";

	string nextItem1URI = "https://happyfunkillclub.com/cards/items/lucky-rabbits-foot/nft.json";
	string nextItem2URI = "https://happyfunkillclub.com/cards/items/intermediate-first-aid-kit/nft.json";
	string nextItem3URI = "https://happyfunkillclub.com/cards/items/potion-of-regeneration/nft.json";
	
	constructor() ERC721("Happy Fun Kill Club Test NFT Items - HFKCTst02", "HFKCT02") {
		nextTokenId = 1;
		item1Cost = 10000000000000000000;
		item2Cost = 25000000000000000000;
		item3Cost = 100000000000000000000;
		nextItem1Cost = 3000000000000000000;
		nextItem2Cost = 15000000000000000000;
		nextItem3Cost = 100000000000000000000;
	}
	
	//Red Coin Token Contract
	address public redCoinContractAddress = 0x4E78Ca0D3B4dcd9b030F61B58BaC521b901545f5; 
	RedCoin redCoinContract = RedCoin(redCoinContractAddress);

	function setChainlinkUpkeeperAddress(address _CLUpkeepAddress) public onlyOwner {
		CLUpkeepAddress = _CLUpkeepAddress;
	}
	function changeItem1Cost(uint256 _item1Cost) public onlyOwner {
		item1Cost = _item1Cost;
	}
	function changeItem2Cost(uint256 _item2Cost) public onlyOwner {
		item2Cost = _item2Cost;
	}
	function changeItem3Cost(uint256 _item3Cost) public onlyOwner {
		item3Cost = _item3Cost;
	}
	function changeItem1URI(string memory _item1URI) private {
		item1URI = _item1URI;
	}
	function changeItem2URI(string memory _item2URI) private {
		item2URI = _item2URI;
	}
	function changeItem3URI(string memory _item3URI) private {
		item3URI = _item3URI;
	}
	function changeNextItem1URI(string memory _nextItem1URI) public onlyOwner {
		nextItem1URI = _nextItem1URI;
	}
	function changeNextItem2URI(string memory _nextItem2URI) public onlyOwner {
		nextItem2URI = _nextItem2URI;
	}
	function changeNextItem3URI(string memory _nextItem3URI) public onlyOwner {
		nextItem3URI = _nextItem3URI;
	}
	//Chainlink Upkeep Calls This Function To Update The Items In RedCoin Store:
	/*
	//View The Redcoin Store Here:
	https://happyfunkillclub.com/?redCoinStore=true&skipPrompt=true
	*/
	
	function setNewItemURIs() public{
		//This function will be called by Chainlink Automation.
		require(msg.sender == CLUpkeepAddress, "Only Chainlink Upkeep Contract Can Call This Function.");
		//Set the New URIs for the Next NFTs for Sale
		changeItem1URI(nextItem1URI);
		changeItem2URI(nextItem2URI);
		changeItem3URI(nextItem3URI);
		//Change the Prices of the Items if The Prices Have Changed
		if (item1Cost != nextItem1Cost){
			item1Cost = nextItem1Cost;
		}
		if (item2Cost != nextItem2Cost){
			item2Cost = nextItem2Cost;
		}
		if (item3Cost != nextItem3Cost){
			item3Cost = nextItem3Cost;
		}
	}
	//If you want to make immediate or manual changes to item URIs and costs in the Store, 
	//Owner Can Do That.
	function manuallySetNewItemURIs() public onlyOwner{
		changeItem1URI(nextItem1URI);
		changeItem2URI(nextItem2URI);
		changeItem3URI(nextItem3URI);
		//Change the Prices of the Items if The Prices Have Changed
		if (item1Cost != nextItem1Cost){
			item1Cost = nextItem1Cost;
		}
		if (item2Cost != nextItem2Cost){
			item2Cost = nextItem2Cost;
		}
		if (item3Cost != nextItem3Cost){
			item3Cost = nextItem3Cost;
		}
	}
	function spendRedCoin(uint8 _whichItem) external payable {
		uint256 itemCost = item3Cost;
		if (_whichItem == 1){
			itemCost = item1Cost;
		}
		else if (_whichItem == 2){
			itemCost = item2Cost;
		}
		//Check Allowance
		redCoinAllowance = redCoinContract.allowance(msg.sender, address(this));
		require (redCoinAllowance >= itemCost, "You must approve this contract to spend your RedCoin.");
		bool sT = false; //Successful Transfer
		sT = redCoinContract.transferFrom(msg.sender, address(this), itemCost);
		if (sT){
			mintTheItem(_whichItem);
		}
	}
	function mintTheItem(uint8 _whichItem) private{		
		if (_whichItem == 3){
			safeMint(msg.sender, item3URI);
		}
		else if (_whichItem == 2){
			safeMint(msg.sender, item2URI);
		}
		else{
			safeMint(msg.sender, item1URI);
		}
	}

	function _beforeTokenTransfer(address from, address to, uint256 tokenId, uint256 batchSize)
		internal
		override(ERC721, ERC721Enumerable)
	{
		super._beforeTokenTransfer(from, to, tokenId, batchSize);
	}
	
	function safeMint(address to, string memory uri) private {
		uint256 tokenId = nextTokenId++;
			_safeMint(to, tokenId);
			_setTokenURI(tokenId, uri);
	}
	function tokenURI(uint256 tokenId) public view override(ERC721, ERC721URIStorage) returns (string memory){
		return super.tokenURI(tokenId);
	}
   
	function _burn(uint256 tokenId) internal override(ERC721, ERC721URIStorage) {
		super._burn(tokenId);
	}
	function supportsInterface(bytes4 interfaceId)
		public
		view
		override(ERC721, ERC721Enumerable, ERC721URIStorage)
		returns (bool)
	{
		return super.supportsInterface(interfaceId);
	}
}