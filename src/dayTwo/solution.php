<?php

$input = file_get_contents(__DIR__ . '/input');
$instructionsArray = array_map(fn($e) => explode(' ', $e), explode("\n", $input));

class Position {
    public int $horizontal = 0;
    public int $depth = 0;
}

$move = function (Position $position, array $instruction): Position
{
    if (count($instruction) < 2) {
        return $position;
    }
    list($command, $value) = $instruction;
    if ($command === 'up') {
        $position->depth -= $value;
    }
    if ($command === 'down') {
        $position->depth += $value;
    }
    if ($command === 'forward') {
        $position->horizontal += $value;
    }
    return $position;
};

$finalPosition = array_reduce($instructionsArray, $move, new Position());
echo 'solution dayTwo, partOne: ' . $finalPosition->depth * $finalPosition->horizontal . "\n";

class AdvancedPosition {
    public int $horizontal = 0;
    public int $depth = 0;
    public int $aim = 0;
}

$moveAdvanced = function (AdvancedPosition $position, array $instruction): AdvancedPosition
{
    if (count($instruction) < 2) {
        return $position;
    }
    list($command, $value) = $instruction;
    if ($command === 'up') {
        $position->aim -= $value;
    }
    if ($command === 'down') {
        $position->aim += $value;
    }
    if ($command === 'forward') {
        $position->horizontal += $value;
        $position->depth += $position->aim * $value;
    }
    return $position;
};

$advancedPosition = array_reduce($instructionsArray, $moveAdvanced, new AdvancedPosition());
echo 'solution dayTwo, partTwo: ' . $advancedPosition->depth * $advancedPosition->horizontal . "\n";