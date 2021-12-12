<?php

$cave = new Cave(__DIR__ . '/input');

printf("1st solution: %d \n" , count($cave->walk()));

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

    public function walk(string $from = 'start', array $touched = []): array
    {
        foreach ($this->graph[$from] as $next) {
            if (in_array($next, $touched) && strtolower($next) === $next) {
                continue;
            }
            if ($next === 'end') {
                $this->wholeWalks[] = [...$touched, $from, $next];
                continue;
            }
            $this->walk($next, [... $touched, $from]);
        }
        return $this->wholeWalks;
    }
}
