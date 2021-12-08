<?php

function inputLines(string $fileName): Generator
{
    $handle = fopen($fileName, 'r');
    while($line = fgets($handle)) {
        yield $line;
    }
}

function outputLine(string $line): string
{
    return explode(' | ', $line)[1];
}

function digitSign(string $linePart): Generator
{
    foreach (explode(' ', $linePart) as $digit) {
        yield trim($digit);
    }
}

function digit(string $sign): ?int
{
    $length = strlen($sign);
    switch ($length) {
        case 2:
            return 1;
        case 3:
            return 7;
        case 4:
            return 4;
        case 7:
            return 8;
        default:
            return null;
    }
    
}

function countIfNotNull($carry, $input): int
{
    return $carry + intval($input !== null);
}

function flatten(array $array): array
{
    return array_reduce($array, fn($carry, $item) => [...$carry, ...$item], []);
}

printf("1st solution: %d \n", array_reduce(
    array_map(
        digit(...),
        flatten(
            array_map(
                iterator_to_array(...),
                array_map(
                    digitSign(...),
                    array_map(
                        outputLine(...),
                        iterator_to_array(inputLines(__DIR__ . '/input'))
                    )
                )
            )
        )
    ),
    countIfNotNull(...)
));
