<?php

	function getAdjacentTiles($tileNumber){
		$adjacentMapping = [
			1 => [2, 4, 5, 6],
			2 => [1, 3, 5, 6, 7],
			3 => [2, 6, 7, 8],
			4 => [1, 5, 9],
			5 => [1, 2, 4, 6, 9, 10],
			6 => [1, 2, 3, 5, 7, 9, 10, 11],
			7 => [2, 3, 6, 8, 10, 11],
			8 => [3, 7, 11],
			9 => [4, 5, 6, 10],
			10 => [5, 6, 7, 9, 11],
			11 => [6, 7, 8, 10]
		];
		return $adjacentMapping[$tileNumber] ?? [];
	}
	//86 is where dead guys go so its got some funny properties here.
	function getAdjacentTilesAndCurrent($tileNumber){
		$adjacentMapping = [
			1 => [1, 2, 4, 5, 6, 86],
			2 => [1, 2, 3, 5, 6, 7, 86],
			3 => [2, 3, 6, 7, 8, 86],
			4 => [1, 4, 5, 9, 86],
			5 => [1, 2, 4, 5, 6, 9, 10, 86],
			6 => [1, 2, 3, 5, 6, 7, 9, 10, 11, 86],
			7 => [2, 3, 6, 7, 8, 10, 11, 86],
			8 => [3, 7, 8, 11, 86],
			9 => [4, 5, 6, 9, 10, 86],
			10 => [5, 6, 7, 9, 10, 11, 86],
			11 => [6, 7, 8, 10, 11, 86],
			86 => [86]
		];
		return $adjacentMapping[$tileNumber] ?? [];
	}

	//$adjacentToTile5 = getAdjacentTiles(5); //Returns [1, 2, 4, 6, 9, 10]

	function calculateRangeScore($playerTile, $targetTile){
		$playerTile = intval($playerTile);
		$targetTile = intval($targetTile);

		//Map of distances between specific tiles
		$distanceMap = [
			1 => [2 => 1, 3 => 2, 4 => 1, 5 => 1, 6 => 1, 7 => 2, 8 => 3, 9 => 2, 10 => 3, 11 => 3],
			2 => [1 => 1, 3 => 1, 4 => 2, 5 => 1, 6 => 1, 7 => 1, 8 => 2, 9 => 2, 10 => 2, 11 => 2],
			3 => [1 => 2, 2 => 1, 4 => 3, 5 => 2, 6 => 1, 7 => 1, 8 => 1, 9 => 2, 10 => 2, 11 => 2],
			4 => [1 => 1, 2 => 2, 3 => 3, 5 => 1, 6 => 2, 7 => 3, 8 => 4, 9 => 1, 10 => 2, 11 => 3],
			5 => [1 => 1, 2 => 1, 3 => 2, 4 => 1, 6 => 1, 7 => 2, 8 => 3, 9 => 1, 10 => 1, 11 => 2],
			6 => [1 => 1, 2 => 1, 3 => 1, 4 => 2, 5 => 1, 7 => 1, 8 => 2, 9 => 1, 10 => 1, 11 => 1],
			7 => [1 => 2, 2 => 1, 3 => 1, 4 => 3, 5 => 2, 6 => 1, 8 => 1, 9 => 2, 10 => 1, 11 => 1],
			8 => [1 => 3, 2 => 2, 3 => 1, 4 => 4, 5 => 3, 6 => 2, 7 => 1, 9 => 3, 10 => 2, 11 => 1],
			9 => [1 => 2, 2 => 2, 3 => 2, 4 => 1, 5 => 1, 6 => 1, 7 => 2, 8 => 3, 10 => 1, 11 => 2],
			10 => [1 => 2, 2 => 2, 3 => 2, 4 => 2, 5 => 1, 6 => 1, 7 => 1, 8 => 2, 9 => 1, 11 => 1],
			11 => [1 => 2, 2 => 2, 3 => 2, 4 => 3, 5 => 2, 6 => 1, 7 => 1, 8 => 1, 9 => 2, 10 => 1]
		];

		//Use the map to find the distance
		$totalDistance = isset($distanceMap[$playerTile][$targetTile]) ? $distanceMap[$playerTile][$targetTile] : 1; //Default to 4 if not found
		return $totalDistance;
	}