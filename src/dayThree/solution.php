<?php

$input = file_get_contents(__DIR__ . '/input');
$reportArray = explode("\n", $input);
array_pop($reportArray); // Ending newline character adds an empty element

$inputLength = count($reportArray);
$columnSum = sumByColumn(array_map('str_split', $reportArray));
$mostCommonBitsByColumn = array_map(fn($column) => intval($column > $inputLength/2), $columnSum);
$leastCommonBitsByColumn = array_map(fn($column) => intval($column < $inputLength/2), $columnSum);

$gammarate = bindec(implode('', $mostCommonBitsByColumn)); // 3527
$epsilonrate = bindec(implode('', $leastCommonBitsByColumn)); // 568

echo "1st Solution: " . $gammarate * $epsilonrate . "\n";

function sumByColumn(array $input): array
{
    $columnSum = [];
    $columnCount = count($input[0]);
    for ($i = 0; $i < $columnCount; $i++) {
        $columnSum[$i] = array_sum(array_column($input, $i));
    }
    return $columnSum;
}

function mostCommonBitForColumn(array $input, int $column): int
{
    $inputLength = count($input);
    $columnSum = sumByColumn(array_map('str_split', $input));
    if ($columnSum[$column] === $inputLength) {
        return 1;
    }
    if ($columnSum[$column] === 0) {
        return 0;
    }
    return intval($columnSum[$column] >= $inputLength/2);
}

function leastCommonBitForColumn(array $input, int $column): int
{
    $inputLength = count($input);
    $columnSum = sumByColumn(array_map('str_split', $input));
    if ($columnSum[$column] === 0) {
        return 0;
    }
    if ($columnSum[$column] === $inputLength) {
        return 1;
    }
    return intval($columnSum[$column] < $inputLength/2);
}

$oxygenTemp = $reportArray;
for($i=0; $i<12;$i++) {
    $mostCommonBit = mostCommonBitForColumn($oxygenTemp, $i);
    $oxygenTemp = array_values(array_filter($oxygenTemp, fn($line) => $line[$i] == $mostCommonBit));
}
$oxygenRate = bindec($oxygenTemp[0]);

$co2scrub = $reportArray;
for($i=0; $i<12;$i++) {
    $leastCommonBit = leastCommonBitForColumn($co2scrub, $i);
    $co2scrub = array_values(array_filter($co2scrub, fn($line) => $line[$i] == $leastCommonBit));
}
$co2scrubRate = bindec($co2scrub[0]);


echo "2nd Solution: " . $oxygenRate * $co2scrubRate . "\n";