<?php

example();
printf("1st soltuion: %d \n", array_sum(
        riskLevels(
            lowPointValues(
                mapFromFile(__DIR__ . '/input')
            )
        )
));

function mapFromFile($inputPath): array
{
    $handle = fopen($inputPath, 'r');
    $map = [];
    while($line = fgets($handle)) {
        $map[] = str_split(trim($line));
    }
    return $map;
}

function lowPointValues(array $map): array
{
    $values = [];
    foreach ($map as $rowIndex => $row) {
        foreach ($row as $column => $value) {
            $upper = $map[$rowIndex-1][$column] ?? PHP_INT_MAX;
            $lower = $map[$rowIndex+1][$column] ?? PHP_INT_MAX;
            $left = $map[$rowIndex][$column-1] ?? PHP_INT_MAX;
            $right = $map[$rowIndex][$column+1] ?? PHP_INT_MAX;
            if ($upper > $value && $lower > $value && $left > $value && $right > $value) {
                $values[] = $value;
            }
        }
    }
    return $values;
}

function riskLevels(array $values): array
{
    return array_map(fn($value) => $value + 1, $values);
}

function example(): void
{
    printf("Example soltuion: %d \n", array_sum(
        riskLevels(
            lowPointValues(
                [
                    [2,1,9,9,9,4,3,2,1,0],
                    [3,9,8,7,8,9,4,9,2,1],
                    [9,8,5,6,7,8,9,8,9,2],
                    [8,7,6,7,8,9,6,7,8,9],
                    [9,8,9,9,9,6,5,6,7,8]
                ]
            )
        )
));
}