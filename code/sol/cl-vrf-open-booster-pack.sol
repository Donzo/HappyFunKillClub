// SPDX-License-Identifier: MIT
pragma solidity ^0.8.13;

import "@chainlink/contracts/src/v0.8/interfaces/VRFCoordinatorV2Interface.sol";
import "@chainlink/contracts/src/v0.8/ConfirmedOwner.sol";
import "@chainlink/contracts/src/v0.8/vrf/VRFConsumerBaseV2.sol";

interface LinkTokenInterface {
	function transfer(address, uint) external returns (bool); //to - value
	function allowance(address, address) external returns (uint256); //owner, spender
	function balanceOf(address) external returns (uint256); 
}
interface HFKCBoosterPack {
	function transferFrom(address, address, uint) external returns (bool); //from to amount
	function allowance(address, address) external returns (uint256); //owner, spender
}
interface HFKCCardMinter {
	function safeMint(address, string[] memory) external; //Address - Random / Card Number
	function transferOwnership(address) external; //Change owner of minting contract if new PACKOPEN contract is deployed.
}
interface HFKCCardURISetter {
	function setURI(uint256) external returns (string memory); //Random / Card Number
}

contract OpenPackContract is VRFConsumerBaseV2, ConfirmedOwner{

	//event RequestFulfilled(uint256 requestId, uint256 randomNum1, uint256 randomNum2, uint256 randomNum3, uint256 randomNum4, uint256 randomNum5, uint256 randomNum6, uint256 randomNum7);
	event RequestFulfilled(uint256 requestId, uint256 randomNum1, uint256 randomNum2, uint256 randomNum3, uint256 randomNum4, uint256 randomNum5);

	//Packs Token Contract
	address public HFKCBoosterPackContractAddress = 0x29C127821CB160672f47d734C97B32c88d38AFD1;
	HFKCBoosterPack HFKCBoosterPackContract = HFKCBoosterPack(HFKCBoosterPackContractAddress);
	
	//URI Setter Contact
	address public HFKCCardURISetterContractAddress = 0x2A6E4D863c0Af387aA2bd40e15dE342877310CC3;
	HFKCCardURISetter HFKCCardURISetterContract = HFKCCardURISetter(HFKCCardURISetterContractAddress);
	
	//Character Card Minter Contract
	address public HFKCCardMinterAddress = 0x05a3F6DC73Be24d3627de207F8f6B9D354E39649;
	HFKCCardMinter HFKCCardMinterContract = HFKCCardMinter(HFKCCardMinterAddress);
	
	address internal msgSender;
	uint256 public packAllowance;
	uint256 public lastRequestID;
	
	uint64 subscriptionID;
	VRFCoordinatorV2Interface COORDINATOR;
	
	//Mapped Random Numbers
	mapping(uint256 => uint256) public mapIdToWord1;
	mapping(uint256 => uint256) public mapIdToWord2;
	mapping(uint256 => uint256) public mapIdToWord3;
	mapping(uint256 => uint256) public mapIdToWord4;
	mapping(uint256 => uint256) public mapIdToWord5;
	//mapping(uint256 => uint256) public mapIdToWord6;
	//mapping(uint256 => uint256) public mapIdToWord7;
	//mapping(uint256 => uint256) public mapIdToWord8;
	//mapping(uint256 => uint256) public mapIdToWord9;
	//mapping(uint256 => uint256) public mapIdToWord10;
	mapping(uint256 => address) public mapIdToAddress; //Address to ID
	mapping(uint256 => bool) public mapIdToFulfilled; //Completion Status to ID
	
	//Array of URIs
	string [] URIArray;
	
	//Might need to change this if the request is failing <----------------------------------------
	uint32 callbackGasLimit = 2420000; //Might need to change this if the request is failing <----------------------------------------
	//Might need to change this if the request is failing <----------------------------------------
	//800000
	
	uint16 requestConfirmations = 3;
	
	//Address LINK - AVAX FUJI Testnet
	address linkAddress = 0x0b9d5D9136855f6FEc3c0993feE6E9CE8a297846;
	bytes32 keyHash = 0x354d2f95da55398f44b7cff77da56283d9c6c829a4bdf1bbcaf2ad6a4d081f61;

	constructor(uint64 subscriptionId) VRFConsumerBaseV2(0x2eD832Ba664535e5886b75D64C46EB9a228C2610) ConfirmedOwner(msg.sender){
		COORDINATOR = VRFCoordinatorV2Interface(
			0x2eD832Ba664535e5886b75D64C46EB9a228C2610 //FUJI COORDINATOR
		);
		subscriptionID = subscriptionId;
	}
	function openPack() external payable {
		msgSender = msg.sender;

		//Check Allowance
		packAllowance = HFKCBoosterPackContract.allowance(msg.sender, address(this));
		require (packAllowance >= 1000000000000000000, "You must approve this contract to spend your pack.");
		spendPackThenRequest(); 
	}
	function spendPackThenRequest() private{
		bool sT = false; //Successful Transfer
		sT = HFKCBoosterPackContract.transferFrom(msg.sender, address(this), 1000000000000000000);
		
		if (sT){
			requestRandomWords();
		}
	}
	/*function requestRandomWords() private returns (uint256 requestId) {
		requestId = requestRandomness(callbackGasLimit, requestConfirmations, 7); //Last Value is Number of Words

		mapIdToAddress[requestId] = msg.sender;
		mapIdToFulfilled[requestId] = false;
		lastRequestID = requestId;
		return requestId;
	}*/
	
	
	function requestRandomWords() private returns (uint256 requestId){
		// Will revert if subscription is not set and funded.
		requestId = COORDINATOR.requestRandomWords(keyHash, subscriptionID, requestConfirmations, callbackGasLimit, 5); //Last Number is How Many Words
		mapIdToAddress[requestId] = msg.sender;
		mapIdToFulfilled[requestId] = false;
		/*s_requests[requestId] = RequestStatus({
			randomWords: new uint256[](0),
			exists: true,
			fulfilled: false
		});*/
		//requestIds.push(requestId);
		lastRequestID = requestId;
		//emit RequestSent(requestId, numWords);
		return requestId;
	}

	function fulfillRandomWords(uint256 _requestId, uint256[] memory _randomWords) internal override {
		require(mapIdToFulfilled[_requestId] == false, 'request fulfilled already');
		mapIdToFulfilled[_requestId] = true;
		mapIdToWord1[_requestId] = (_randomWords[0] % 1000) + 1;
		mapIdToWord2[_requestId] = (_randomWords[1] % 1000) + 1; //Store it.
		mapIdToWord3[_requestId] = (_randomWords[2] % 1000) + 1;
		mapIdToWord4[_requestId] = (_randomWords[3] % 1000) + 1;
		mapIdToWord5[_requestId] = (_randomWords[4] % 1000) + 1;
		//mapIdToWord6[_requestId] = (_randomWords[5] % 1000) + 1;
		//mapIdToWord7[_requestId] = (_randomWords[6] % 1000) + 1;
		//mapIdToWord8[_requestId] = flatRanNum8; //Store it.
		//mapIdToWord9[_requestId] = flatRanNum9; //Store it.
		//mapIdToWord10[_requestId] = flatRanNum10; //Store it.
		
		mintCards(_requestId);
		emit RequestFulfilled(_requestId, mapIdToWord1[_requestId], mapIdToWord2[_requestId], mapIdToWord3[_requestId], mapIdToWord4[_requestId], mapIdToWord5[_requestId]); //ID, NUM1, NUM2
		//emit RequestFulfilled(_requestId, mapIdToWord1[_requestId], mapIdToWord2[_requestId], mapIdToWord3[_requestId], mapIdToWord4[_requestId], mapIdToWord5[_requestId], mapIdToWord6[_requestId], mapIdToWord7[_requestId]); //ID, NUM1, NUM2
	}
	function mintCards(uint256 _requestId) private{
		delete URIArray;
		URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord1[_requestId]));
		URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord2[_requestId]));
		URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord3[_requestId]));
		URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord4[_requestId]));
		URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord5[_requestId]));
		//URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord6[_requestId]));
		//URIArray.push(HFKCCardURISetterContract.setURI(mapIdToWord7[_requestId]));
		
		HFKCCardMinterContract.safeMint(mapIdToAddress[_requestId], URIArray);
	}
	//Edit URISetter Address
	function changeHFKCCardURISetterContractAddress(address _newAddress) public onlyOwner {
		HFKCCardURISetterContractAddress = _newAddress;
	}
	//Transfer Ownership of Minter Contract if this contract needs to be redeployed
	function transferCCMinterOwnership(address _toAddress) public onlyOwner{
		//HFKCCardMinter HFKCCardMinterContract = HFKCCardMinter(HFKCCardMinterAddress);
		   HFKCCardMinterContract.transferOwnership(_toAddress);
	}
	//Change Character Card Minter Address if New Contract is Deployed
	function changeCardMinterAddress(address _newCardMinterAddress) public onlyOwner {
		HFKCCardMinterAddress = _newCardMinterAddress;
	}
	//Change Address of Booster Packs
	function changeHFKCBoostPackContractAddress(address _HFKCBoosterPackContractAddress) public onlyOwner {
		HFKCBoosterPackContractAddress = _HFKCBoosterPackContractAddress;
	}
	//Withdraw Link
	function withdrawLink() public onlyOwner{
		LinkTokenInterface link = LinkTokenInterface(linkAddress);
		require(link.transfer(address(owner()), link.balanceOf(address(this))), 'Unable to transfer');
	}
	//Withdraw AVAX
	function withdrawAVAX(uint256 amount) public onlyOwner{
		address payable to = payable(address(owner()));
		to.transfer(amount);
	}
}