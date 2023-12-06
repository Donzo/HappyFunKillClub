//SPDX-License-Identifier: MIT
pragma solidity ^0.8.9;

import "@openzeppelin/contracts@4.9.0/token/ERC721/ERC721.sol";
import "@openzeppelin/contracts@4.9.0/token/ERC721/extensions/ERC721URIStorage.sol";
import "@openzeppelin/contracts@4.9.0/token/ERC721/extensions/ERC721Enumerable.sol";
import "@openzeppelin/contracts@4.9.0/access/Ownable.sol";

//Character Card Contract
contract HappyFunKillClubCardMinter is ERC721, ERC721Enumerable, ERC721URIStorage, Ownable {
	
	uint256 public nextTokenId;

	constructor() ERC721("Happy Fun Kill Club Test NFT - HFKCTst01", "HFKCT01") {
		nextTokenId = 1;
	}
	
	function _beforeTokenTransfer(address from, address to, uint256 tokenId, uint256 batchSize)
		internal
		override(ERC721, ERC721Enumerable)
	{
		super._beforeTokenTransfer(from, to, tokenId, batchSize);
	}
	
	function safeMint(address to, string[] memory uri) public onlyOwner {
		for(uint8 i = 0; i<uri.length; i++){
			uint256 tokenId = nextTokenId++;
			_safeMint(to, tokenId);
			_setTokenURI(tokenId, uri[i]);
		}
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