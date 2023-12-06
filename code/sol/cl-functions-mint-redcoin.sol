//SPDX-License-Identifier: MIT
pragma solidity 0.8.19;

import {FunctionsClient} from "@chainlink/contracts/src/v0.8/functions/dev/v1_0_0/FunctionsClient.sol";
import {ConfirmedOwner} from "@chainlink/contracts/src/v0.8/shared/access/ConfirmedOwner.sol";
import {FunctionsRequest} from "@chainlink/contracts/src/v0.8/functions/dev/v1_0_0/libraries/FunctionsRequest.sol";

interface RedCoinToken {
	function mint(address, uint256) external;  
	function transferOwnership(address) external; //from and to
}


contract RedCoinCheck is FunctionsClient, ConfirmedOwner {
	using FunctionsRequest for FunctionsRequest.Request;

	//State variables to store the last request ID, response, and error
	bytes32 public s_lastRequestId;
	bytes public s_lastResponse;
	bytes public s_lastError;
	uint32 gasLimit = 300000; //Callback gas limit
	bytes32 donID = 0x66756e2d6176616c616e6368652d66756a692d31000000000000000000000000; //donID - Avalanche Fuji
	address private redCoinContractAddress; //SET IN CONSTRUCTOR, REMEMBER TO CHANGE IF DEPLOYING NEW REDCOIN TOKEN
	address public mintersAddress;
	string public redCoins;
	uint256 public redCoinsToMint;
	
	error UnexpectedRequestID(bytes32 requestId);

	//Event that log buy operation
	event MintRedCoins(address buyer, uint256 amountOfAVAX);

	 //Fetch RedCoint Amount
	string source = 
		"const wallet = args[0];"
		"const tkn = args[1];"
		"const apiResponse = await Functions.makeHttpRequest({"
		"url: `https://happyfunkillclub.com/code/php/mint-redcoin-check.php?wallet=${wallet}&tkn=${tkn}`"
		"});"
		"if (apiResponse.error) {"
		"throw Error('Request failed');"
		"}"
		"const { data } = apiResponse;"
		"return Functions.encodeString(data.redCoins);";

	//Event to log responses
	event Response(
		bytes32 indexed requestId,
		string redCoins,
		bytes response,
		bytes err
	);

	//Avalanche Fuji Router
	address router = 0xA9d587a00A31A52Ed70D6026794a8FC5E2F5dCb0;
 
	constructor() FunctionsClient(router) ConfirmedOwner(msg.sender) {
		redCoinContractAddress = 0x4E78Ca0D3B4dcd9b030F61B58BaC521b901545f5; 
		//Change This if Deploying New REDCOIN contract
	}

	function checkAndMintRedCoins (uint64 _subscriptionId, string[] calldata _args) public payable{
		require(msg.value > 10000000000000000, "Send .1 AVAX to mint REDCOINS.");
		mintersAddress = msg.sender;
		//emit the event
		emit MintRedCoins(msg.sender, msg.value);
		sendRequest(_subscriptionId, _args);
	}

	function sendRequest(
		uint64 subscriptionId,
		string[] calldata args
	) public returns (bytes32 requestId) {
		FunctionsRequest.Request memory req;
		req.initializeRequestForInlineJavaScript(source);
		if (args.length > 0) req.setArgs(args);

		s_lastRequestId = _sendRequest(
			req.encodeCBOR(),
			subscriptionId,
			gasLimit,
			donID
		);

		return s_lastRequestId;
	}

	function strToUint(string memory _str) public pure returns(uint256 res, bool err) {
	
		for (uint256 i = 0; i < bytes(_str).length; i++) {
			if ((uint8(bytes(_str)[i]) - 48) < 0 || (uint8(bytes(_str)[i]) - 48) > 9) {
				return (0, false);
			}
			res += (uint8(bytes(_str)[i]) - 48) * 10**(bytes(_str).length - i - 1);
		} 
		return (res, true);
	}
	function transferRedCoinContractOwnership(address _toAddress) public onlyOwner{
		   RedCoinToken redCoinContract = RedCoinToken(redCoinContractAddress);
		   redCoinContract.transferOwnership(_toAddress);
	}
	function fulfillRequest(
		bytes32 requestId,
		bytes memory response,
		bytes memory err
	) internal override {
		if (s_lastRequestId != requestId) {
			revert UnexpectedRequestID(requestId); //Check if request IDs match
		}
		//Update the contract's state variables with the response and any errors
		s_lastResponse = response;
		redCoins = string(response);
		s_lastError = err;
	   
		//Convert string to uint and check for errors
		(uint256 convertedValue, bool conversionSuccess) = strToUint(redCoins);
		if (conversionSuccess) {
			redCoinsToMint = convertedValue;
			//Mint if More Than 0 RedCoins
			if (redCoinsToMint > 0){
				RedCoinToken redCoinContract = RedCoinToken(redCoinContractAddress);
				redCoinsToMint *= 10**18;
				redCoinContract.mint(mintersAddress, redCoinsToMint);
			}
		}
		//Emit an event to log the response
		emit Response(requestId, redCoins, s_lastResponse, s_lastError);
	}
	function withdraw() public onlyOwner {
		uint256 ownerBalance = address(this).balance;
		require(ownerBalance > 0, "Owner has no balance to withdraw");

		(bool sent,) = msg.sender.call{value: address(this).balance}("");
			require(sent, "Failed to send user balance back to the owner");
	}
}
