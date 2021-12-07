<?php

$positions = array_map(intval(...), explode(',', file_get_contents(__DIR__ . '/input')));

$consumptionA = min(
    array_reduce(
        range(min($positions), max($positions)), // Possible positions
        function ($carry, $positionProposal) use ($positions) {
            $carry[$positionProposal] = array_sum(
                array_map(
                    fn($position) => abs($position - $positionProposal), // Consumption for a position is the distance
                    $positions
                )
            );
            return $carry;
        },
        []
    )
);

printf("1st solution: %d \n", $consumptionA);

$consumptionB = min(
    array_reduce(
        range(min($positions), max($positions)), // Possible positions
        function ($carry, $positionProposal) use ($positions) {
            $carry[$positionProposal] = array_sum(
                array_map(
                    function($position) use ($positionProposal) {
                        $d = abs($position - $positionProposal); // Distance
                        return ($d+$d*$d)/2; // Sum of series' first n term: S(n) = n(a1+an)/2. This case: $d(1+$d)/2
                    }, // Consumption for a position
                    $positions
                )
            );
            return $carry;
        },
        []
    )
);

printf("2nd solution: %d \n", $consumptionB);