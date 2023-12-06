//SPDX-License-Identifier: MIT
pragma solidity ^0.8.9;

//import "@openzeppelin/contracts/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts@4.3.0/token/ERC20/ERC20.sol";
import "@openzeppelin/contracts@4.3.0/token/ERC20/extensions/ERC20Burnable.sol";
import "@openzeppelin/contracts@4.3.0/access/Ownable.sol";

contract HFKCBoosterPack is ERC20, ERC20Burnable, Ownable {
	constructor() ERC20("HFKCBoosterPackTest01", "PACKS") {
		_mint(msg.sender, 10000 * 10 ** decimals());
	}
}