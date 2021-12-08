<?php

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

printf("2nd solution: %d \n", array_sum(
    array_map(
        fn($line) => translateOutput(
            findDigits(
                 array_map(
                    inputSignSort(...),
                    distinct(
                        iterator_to_array(
                            digitSign(
                                inputLine($line)
                            )
                        )
                    )
                )
            ), 
            array_map(
                inputSignSort(...),
                iterator_to_array(
                    digitSign(
                        outputLine($line)
                    )
                )
            )
        ),
        iterator_to_array(
            inputLines(__DIR__ . '/input')
        )
    )
));

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

function inputLine(string $line): string
{
    return explode(' | ', $line)[0];
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

function inputSignSort(string $sign): string
{
    $signArray = str_split($sign);
    sort($signArray);
    return implode('', $signArray);
}

function distinct(array $items): array
{
    return array_flip(array_reduce($items, function ($carry, $item) {
        if (! array_key_exists($item, $carry)) {
            $carry[$item] = count($carry);
        }
        return $carry;
    }, []));
}

function findOne(array $signs): ?string
{
    $ones = array_values(array_filter($signs, fn($sign) => strlen($sign) === 2));
    return $ones[0] ?? null;
}

function findSeven(array $signs): ?string
{
    $sevens = array_values(array_filter($signs, fn($sign) => strlen($sign) === 3));
    return $sevens[0] ?? null;
}

function findFour(array $signs): ?string
{
    $fours = array_values(array_filter($signs, fn($sign) => strlen($sign) === 4));
    return $fours[0] ?? null;
}

function findEight(array $signs): ?string
{
    $eights = array_values(array_filter($signs, fn($sign) => strlen($sign) === 7));
    return $eights[0] ?? null;
}

function findSix(array $signs, ?string $one): ?string
{
    if ($one === null) {
        return null;
    }
    $sixes = array_values(array_filter($signs, fn($sign) => strlen($sign) === 6 && count(array_intersect(str_split($sign), str_split($one))) === 1));
    return $sixes[0] ?? null;
}

function findNine(array $signs, ?string $one, ?string $three): ?string
{
    if ($one === null) {
        return null;
    }
    $sixes = array_values(array_filter(
        $signs,
        fn($sign) =>
                strlen($sign) === 6
            && count(array_intersect(str_split($sign), str_split($one))) === 2
            && count(array_intersect(str_split($sign), str_split($three))) === 5
    ));
    return $sixes[0] ?? null;
}

function findThree(array $signs, ?string $one): ?string
{
    if ($one === null) {
        return null;
    }
    $sixes = array_values(array_filter($signs, fn($sign) => strlen($sign) === 5 && count(array_diff(str_split($sign), str_split($one))) === 3));
    return $sixes[0] ?? null;
}

function findZero(array $signs, ?string $six, ?string $nine): ?string
{
    if ($six === null || $nine === null) {
        return null;
    }
    $zeros = array_values(
        array_filter($signs, fn($sign) => strlen($sign) === 6 && $sign !== $six && $sign !== $nine)
    );
    return $zeros[0] ?? null;
}

function findTwo(array $signs, ?string $nine): ?string
{
    if ($nine === null) {
        return null;
    }
    $twos = array_values(array_filter(
        $signs,
        fn($sign) => strlen($sign) === 5 && count(array_diff(str_split($sign), str_split($nine))) === 1
    ));
    return $twos[0] ?? null;
}

function findFive(array $signs, ?string $two, ?string $three, ?string $nine): ?string
{
    if ($nine === null) {
        return null;
    }
    $fives = array_values(array_filter(
        $signs,
        fn($sign) => strlen($sign) === 5 && count(array_diff(str_split($sign), str_split($nine))) === 0 && $sign !== $two && $sign !== $three
    ));
    return $fives[0] ?? null;
}

function findDigits (array $input): array
{
    $one = findOne($input);
    $four = findFour($input) ?? '';
    $seven = findSeven($input) ?? '';
    $eight = findEight($input) ?? '';
    $three = findThree($input, $one) ?? '';
    $six = findSix($input, $one) ?? '';
    $nine = findNine($input, $one, $three) ?? '';
    $two = findTwo($input, $nine) ?? '';
    $five = findFive($input, $two, $three, $nine) ?? '';
    $zero = findZero($input, $six, $nine) ?? '';
    return [
        0 => $zero,
        1 => $one,
        2 => $two,
        3 => $three,
        4 => $four,
        5 => $five,
        6 => $six,
        7 => $seven,
        8 => $eight,
        9 => $nine
    ];
}

function translateOutput(array $digits, array $output): string
{
    $missingNumbers = array_diff([0,1,2,3,4,5,6,7,8,9], array_keys(array_filter($digits)));
    $missingNumber = array_pop($missingNumbers);
    return implode('', array_map(
        fn($digit) => array_search($digit, $digits) !== false ? array_search($digit, $digits) : $missingNumber
    , $output));
}
