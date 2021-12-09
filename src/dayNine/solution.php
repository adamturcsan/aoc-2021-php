<?php

example();
printf("1st soltuion: %d \n", array_sum(
        riskLevels(
            lowPointValues(
                lowPoints(
                    mapFromFile(__DIR__ . '/input')
                )
            )
        )
));
secondExample();
printf("2nd soltuion: %d \n",
    array_product(
        getTopThree(
            findBasins(
                lowPoints(
                    mapFromFile(__DIR__ . '/input')
                ),
                mapFromFile(__DIR__ . '/input')
            )
        )
    )
);

function mapFromFile($inputPath): array
{
    $handle = fopen($inputPath, 'r');
    $map = [];
    while($line = fgets($handle)) {
        $map[] = array_map(intval(...), str_split(trim($line)));
    }
    return $map;
}

function lowPoints(array $map): array
{
    $points = [];
    foreach ($map as $rowIndex => $row) {
        foreach ($row as $column => $value) {
            $upper = $map[$rowIndex-1][$column] ?? PHP_INT_MAX;
            $lower = $map[$rowIndex+1][$column] ?? PHP_INT_MAX;
            $left = $map[$rowIndex][$column-1] ?? PHP_INT_MAX;
            $right = $map[$rowIndex][$column+1] ?? PHP_INT_MAX;
            if ($upper > $value && $lower > $value && $left > $value && $right > $value) {
                $points[] = [
                    $rowIndex,
                    $column,
                    $value
                ];
            }
        }
    }
    return $points;
}

function lowPointValues(array $points): array
{
    return array_column($points, 2);
}

function riskLevels(array $values): array
{
    return array_map(fn($value) => $value + 1, $values);
}

function getTopThree(array $values): array
{
    $sortableValues = $values;
    rsort($sortableValues);
    $largests = array_slice($sortableValues, 0, 3);
    return $largests;
}

// If basins are touching eachother, it won't work...
function findBasins(array $lowPoints, array $map): array
{
    $sizes = [];
    foreach ($lowPoints as $point) {
        $sizes[] = findLocalBasinSize(
            findLocalBasinElements($point, $map)
        );
    }
    return $sizes;
}

function findLocalBasinSize(array $localBasinElements): int
{
    return count(array_unique($localBasinElements, SORT_REGULAR));
}

function findLocalBasinElements(array $point, array $map): array
{
    $down = isset($map[$point[0] - 1][$point[1]]) ? [$point[0] - 1, $point[1], $map[$point[0] - 1][$point[1]]] : null;
    $up = isset($map[$point[0] + 1][$point[1]]) ? [$point[0] + 1, $point[1], $map[$point[0] + 1][$point[1]]] : null;
    $left = isset($map[$point[0]][$point[1] - 1]) ? [$point[0], $point[1] - 1, $map[$point[0]][$point[1] - 1]] : null;
    $right = isset($map[$point[0]][$point[1] + 1]) ? [$point[0], $point[1] + 1, $map[$point[0]][$point[1] + 1]] : null;
    $elements = [
        [$point]
    ];
    if ($down !== null && $down[2] > $point[2] && $down[2] !== 9) $elements[] = [$down, ...findLocalBasinElements($down, $map)];
    if ($up !== null && $up[2] > $point[2] && $up[2] !== 9) $elements[] = [$up, ...findLocalBasinElements($up, $map)];
    if ($left !== null && $left[2] > $point[2] && $left[2] !== 9) $elements[] = [$left, ...findLocalBasinElements($left, $map)];
    if ($right !== null && $right[2] > $point[2] && $right[2] !== 9) $elements[] = [$right, ...findLocalBasinElements($right, $map)];
    return flatten($elements);
}

function flatten(array $array): array
{
    return array_reduce($array, fn($carry, $item) => [...$carry, ...$item], []);
}

function example(): void
{
    printf("Example soltuion: %d \n", array_sum(
        riskLevels(
            lowPointValues(
                lowPoints(
                    [
                        [2,1,9,9,9,4,3,2,1,0],
                        [3,9,8,7,8,9,4,9,2,1],
                        [9,8,5,6,7,8,9,8,9,2],
                        [8,7,6,7,8,9,6,7,8,9],
                        [9,8,9,9,9,6,5,6,7,8]
                    ]
                )
            )
        )
));
}

function secondExample(): void
{
    printf("Second example soltuion: %d \n", array_product(
            getTopThree(
                findBasins(
                    lowPoints(
                        [
                            [2,1,9,9,9,4,3,2,1,0],
                            [3,9,8,7,8,9,4,9,2,1],
                            [9,8,5,6,7,8,9,8,9,2],
                            [8,7,6,7,8,9,6,7,8,9],
                            [9,8,9,9,9,6,5,6,7,8]
                        ]
                    ),
                    [
                        [2,1,9,9,9,4,3,2,1,0],
                        [3,9,8,7,8,9,4,9,2,1],
                        [9,8,5,6,7,8,9,8,9,2],
                        [8,7,6,7,8,9,6,7,8,9],
                        [9,8,9,9,9,6,5,6,7,8]
                    ]
                )
            )
        )
    );
}