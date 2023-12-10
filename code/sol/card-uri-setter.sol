//SPDX-License-Identifier: MIT
pragma solidity ^0.8.9;

contract HFKCCardURISetter{

	//These values are all kind of arbitrary right now.
	//When the full set of cards has been created,
	//I will thoughtfully adjust the likelihood of each card appearing.	

	function setURI(uint256 _ranNumber) public pure returns (string memory){
		string memory URL;
        if (_ranNumber < 10){
			URL = "https://happyfunkillclub.com/cards/characters/sir-nibblet-crossfield/nft.json";
		}
		else if (_ranNumber < 20){
			URL = "https://happyfunkillclub.com/cards/characters/clyde-derringer/nft.json";
		}
		else if (_ranNumber < 30){
			URL = "https://happyfunkillclub.com/cards/characters/kira-musashi/nft.json";
		}
		else if (_ranNumber < 40){
			URL ="https://happyfunkillclub.com/cards/characters/edmund-arrowfly/nft.json";
		}
		else if (_ranNumber < 50){
			URL = "https://happyfunkillclub.com/cards/characters/freyja-snowbinder/nft.json";
		}
		else if (_ranNumber < 100){
			URL = "https://happyfunkillclub.com/cards/characters/agent-mason/nft.json";
		}
		else if (_ranNumber < 140){
			URL = "https://happyfunkillclub.com/cards/characters/xyrex-nebulae/nft.json"; //7
		}
		else if (_ranNumber < 180){
			URL = "https://happyfunkillclub.com/cards/characters/necrocleric-malachor/nft.json"; //8
		}
		else if (_ranNumber < 220){
			URL = "https://happyfunkillclub.com/cards/characters/lyana-greenmantle/nft.json"; //9
		}
		else if (_ranNumber < 260){
			URL = "https://happyfunkillclub.com/cards/characters/solace-etherbound/nft.json"; //10
		}
		else if (_ranNumber < 300){
			URL = "https://happyfunkillclub.com/cards/characters/sierra-sightline-kestrel/nft.json"; //11
		}
		else if (_ranNumber < 340){
			URL = "https://happyfunkillclub.com/cards/characters/ragnar-vane/nft.json"; //12
		}
		else if (_ranNumber < 380){
			URL = "https://happyfunkillclub.com/cards/characters/callow-skyshriek/nft.json"; //13
		}
		else if (_ranNumber < 420){
			URL = "https://happyfunkillclub.com/cards/characters/sir-mortan-the-undying/nft.json";  //14
		}
		else if (_ranNumber < 460){
			URL = "https://happyfunkillclub.com/cards/characters/zhan-shen/nft.json";  //15
		}
		else if (_ranNumber < 500){
			URL = "https://happyfunkillclub.com/cards/characters/tukkuk-nanook/nft.json";  //16
		}
		else if (_ranNumber < 540){
			URL = "https://happyfunkillclub.com/cards/characters/mycelius-rex/nft.json"; //17
		}
		else if (_ranNumber < 680){
			URL = "https://happyfunkillclub.com/cards/characters/eron-hushblade/nft.json"; //18
		}
		else if (_ranNumber < 720){
			URL = "https://happyfunkillclub.com/cards/characters/lorien-spectrum/nft.json"; //19
		}
		else if (_ranNumber <= 860){
			URL = "https://happyfunkillclub.com/cards/characters/frankie-stubbs/nft.json"; //20
		}	
		else if (_ranNumber < 900){
			URL = "https://happyfunkillclub.com/cards/characters/john-riptide-mctavish/nft.json"; //21
		}
		else if (_ranNumber < 920){
			URL = "https://happyfunkillclub.com/cards/characters/kaelo-vex/nft.json"; //22
		}
		else if (_ranNumber < 940){
			URL = "https://happyfunkillclub.com/cards/characters/valor-wildsong/nft.json"; //23
		}
		else if (_ranNumber < 960){
			URL = "https://happyfunkillclub.com/cards/characters/ursaon-ironpelt/nft.json";  //24
		}
		else if (_ranNumber < 970){
			URL = "https://happyfunkillclub.com/cards/characters/thump-the-ripper/nft.json";  //25
		}
		else if (_ranNumber < 990){
			URL = "https://happyfunkillclub.com/cards/characters/azuron-the-starweaver/nft.json";  //26
		}
		else if (_ranNumber <= 1000){
			URL = "https://happyfunkillclub.com/cards/characters/mancala-naga/nft.json"; //27
		}
        return URL;
    }
}
