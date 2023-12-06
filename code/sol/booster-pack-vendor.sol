//SPDX-License-Identifier: MIT
pragma solidity ^0.8.4;

import "@openzeppelin/contracts@4.9.0/access/Ownable.sol";
import "@openzeppelin/contracts@4.9.0/security/ReentrancyGuard.sol";

interface HFKCBoosterPack {
	function transferFrom(address, address, uint) external returns (bool); //from to amount
	function transfer(address, uint256) external returns (bool);
	function allowance(address, address) external returns (uint256); //owner, spender
	function balanceOf(address) external returns (uint256); 
}

contract HFKCBoosterPackVendor is Ownable, ReentrancyGuard {

	//Our Token Contract
	HFKCBoosterPack HFKCBoosterPackContract;
	address public HFKCBoosterPackContractAddress = 0x6eBA23766A6F905BD0C70Aec6180BE182caCD4f7;

	//token price per AVAX
	uint256 public tokensPerAVAX = 10;
	//0.1 AVAX Price Initally

	//Event that log buy operation
	event BuyTokens(address buyer, uint256 amountOfAVAX, uint256 amountOfTokens);

	constructor(address _tokenAddress) {
		HFKCBoosterPackContract = HFKCBoosterPack(_tokenAddress);
	}

	/**
	* @notice Allow users to buy token for ETH
	*/
	function buyTokens() public payable nonReentrant returns (uint256 tokenAmount) {
		require(msg.value > 100000000, "Send MORE AVAX to buy PACKS token");

		uint256 amountToBuy = msg.value * tokensPerAVAX;

		//check if the Vendor Contract has enough amount of tokens for the transaction
		uint256 vendorBalance = HFKCBoosterPackContract.balanceOf(address(this));
		require(vendorBalance >= amountToBuy, "Not ENOUGH PACKS tokens in this Vendor Contract");

		//Transfer token to the msg.sender
		(bool sent) = HFKCBoosterPackContract.transfer(msg.sender, amountToBuy);
		require(sent, "Failed to transfer token to user");

		//emit the event
		emit BuyTokens(msg.sender, msg.value, amountToBuy);

		return amountToBuy;
	}

	/**
	* @notice Allow the owner of the contract to withdraw ETH
	*/
	function withdraw() public onlyOwner {
		uint256 ownerBalance = address(this).balance;
		require(ownerBalance > 0, "Owner has not balance to withdraw");

		(bool sent,) = msg.sender.call{value: address(this).balance}("");
		require(sent, "Failed to send user balance back to the owner");
	}
}

/***

WITH REENTRANCY GUARD AND BETTER DECIMAL PRECISION

//SPDX-License-Identifier: MIT
pragma solidity ^0.8.4;

import "@openzeppelin/contracts@4.9.0/access/Ownable.sol";
import "@openzeppelin/contracts@4.9.0/security/ReentrancyGuard.sol";

interface HFKCBoosterPack {
	function transferFrom(address, address, uint) external returns (bool); //from to amount
	function transfer(address, uint256) external returns (bool);
	function allowance(address, address) external returns (uint256); //owner, spender
	function balanceOf(address) external returns (uint256); 
}

contract HFKCBoosterPackVendor is Ownable, ReentrancyGuard {

	//Token Contract
	HFKCBoosterPack HFKCBoosterPackContract;
	address public HFKCBoosterPackContractAddress = 0x6eBA23766A6F905BD0C70Aec6180BE182caCD4f7;

	//token price per AVAX
	uint256 public tokensPerAVAX = 10 * 10**18; // 10 PACKS per AVAX;
	//0.1 AVAX Price Initally

	//Event that log buy operation
	event BuyTokens(address buyer, uint256 amountOfAVAX, uint256 amountOfTokens);

	constructor(address _tokenAddress) {
		HFKCBoosterPackContract = HFKCBoosterPack(_tokenAddress);
	}

	/**
	* @notice Allow users to buy token for ETH
	*/
	function buyTokens() public payable nonReentrant returns (uint256 tokenAmount) {
		require(msg.value > 100000000, "Send MORE AVAX to buy PACKS token");

        uint256 amountToBuy = (msg.value * tokensPerAVAX) / 10**18;
        
		//check if the Vendor Contract has enough amount of tokens for the transaction
		uint256 vendorBalance = HFKCBoosterPackContract.balanceOf(address(this));
		require(vendorBalance >= amountToBuy, "Not ENOUGH PACKS tokens in this Vendor Contract");

		//Transfer token to the msg.sender
		(bool sent) = HFKCBoosterPackContract.transfer(msg.sender, amountToBuy);
		require(sent, "Failed to transfer PACKS to user");

		//emit the event
		emit BuyTokens(msg.sender, msg.value, amountToBuy);

		return amountToBuy;
	}

	/**
	* @notice Allow the owner of the contract to withdraw ETH
	*/
	function withdraw() public onlyOwner {
		uint256 ownerBalance = address(this).balance;
		require(ownerBalance > 0, "Owner has not balance to withdraw");

		(bool sent,) = msg.sender.call{value: address(this).balance}("");
		require(sent, "Failed to send user balance back to the owner");
	}
}

****/