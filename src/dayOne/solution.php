<?php

$input = file_get_contents(__DIR__ . '/input');
$inputArray = array_map(fn($e) => intval($e), explode("\n", $input));

$counter1st = 0;
foreach ($inputArray as $key => $measurement) {
    if (isset($inputArray[$key - 1])) {
        $counter1st += intval(($measurement - $inputArray[$key-1]) > 0);
    }
}
echo '1st part solution: '.$counter1st . "\n\n";

$prevWindowSum = null;
$counter2nd = 0;
foreach ($inputArray as $key => $measurement) {
    if (isset($inputArray[$key - 1]) && isset($inputArray[$key - 2])) {
        $windowSum = $measurement + $inputArray[$key - 1] + $inputArray[$key - 2];
        if ($prevWindowSum !== null) {
            $counter2nd += intval(($prevWindowSum < $windowSum));
        }
        $prevWindowSum = $windowSum;
    }
}
echo '2nd part solution: '.$counter2nd . "\n\n";