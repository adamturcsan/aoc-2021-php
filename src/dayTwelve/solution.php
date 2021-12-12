<?php

$cave = new Cave(__DIR__ . '/input');
printf("1st solution: %d \n" , count($cave->walk()));
$secondCave = new Cave(__DIR__ . '/input');
printf("2nd solution: %d \n" , count($secondCave->walk(mayWalkOnceAgain: true)));

class Cave
{
    private array $graph = [];
    private array $wholeWalks = [];
    
    public function __construct(string $inputFilePath) {
        $handle = fopen($inputFilePath, 'r');
        $paths = [];
        while ($line = fgets($handle)) {
            $path = array_map(trim(...), explode('-', $line));
            $this->graph[$path[0]][] = $path[1];
            $this->graph[$path[1]][] = $path[0];
        }
    }

    public function walk(string $from = 'start', array $touched = [], bool $mayWalkOnceAgain = false): array
    {
        foreach ($this->graph[$from] as $next) {
            if (! $this->canWalkAgain([... $touched, $from], $next, $mayWalkOnceAgain)) {
                continue;
            }
            if ($next === 'end') {
                $this->wholeWalks[] = [...$touched, $from, $next];
                continue;
            }
            $this->walk($next, [... $touched, $from], $mayWalkOnceAgain);
        }
        return $this->wholeWalks;
    }

    private function canWalkAgain(array $touched, string $point, bool $mayWalkOnceAgain): bool
    {
        $smallTouches = array_filter($touched, fn($e) => strtolower($e) === $e);
        $valuesCount = array_count_values($smallTouches);
        return strtolower($point) !== $point
            || ! in_array($point, $smallTouches)
            || (
                   $point !== 'start'
                && $point !== 'end'
                && ! in_array(2, $valuesCount, true)
                && $mayWalkOnceAgain
            );
    }
}
