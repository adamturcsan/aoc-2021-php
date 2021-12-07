<?php

$startAt = hrtime(true);
$pool = new FishPool(file_get_contents(__DIR__ . '/input'));

echo "1st solution: " . $pool->simulate(80) . "\n";
echo "2nd solution: " . $pool->simulate(256) . "\n";

echo "Execution time: " . (hrtime(true) - $startAt)/1000000 . "ms \n";

class FishPool
{
    private array $fishCountByAge = [];

    public function __construct(string $input)
    {
        $ages = array_map(fn(string $age) => intval($age), explode(',', $input));
        $this->fishCountByAge = array_reduce(
            $ages,
            function($carry, $age) {
                $carry[$age]++;
                return $carry;
            },
            [0,0,0,0,0,0,0,0,0]
        );
    }

    public function simulate(int $forDays): int
    {
        $simulationCounts = $this->fishCountByAge;
        for ($i = $forDays; $i > 0; $i--) {
            $simulationCounts = [
                $simulationCounts[1],
                $simulationCounts[2],
                $simulationCounts[3],
                $simulationCounts[4],
                $simulationCounts[5],
                $simulationCounts[6],
                $simulationCounts[7] + $simulationCounts[0],
                $simulationCounts[8],
                $simulationCounts[0],
            ];
        }
        return array_sum($simulationCounts);
    }
}
