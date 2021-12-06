<?php

$input = file_get_contents(__DIR__ . '/input');

$pool = new FishPool(...parseInput($input));

echo "1st solution: " . $pool->fastSimulation(80) . "\n";
echo "2nd solution: " . $pool->fastSimulation(256) . "\n";

function parseInput(string $input): array
{
    $fishAges = explode(',', $input);
    return array_map(fn(int $age) => new Fish($age), $fishAges);
}

class FishPool
{
    private array $fishes;
    private SplFixedArray $simulationPool;
    private array $fishCountByAge = [];

    public function __construct(Fish ... $fishes)
    {
        $this->fishes = $fishes;
        $this->fishCountByAge = array_reduce(
            $fishes,
            function($carry, $fish) {
                $carry[$fish->getAge()]++;
                return $carry;
            },
            [0,0,0,0,0,0,0,0,0]
        );
    }

    public function fastSimulation(int $forDays): int
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
                $simulationCounts[7]+$simulationCounts[0],
                $simulationCounts[8],
                $simulationCounts[0],
            ];
        }
        return array_sum($simulationCounts);
    }
    
    public function simulate(int $forDays): int
    {
        $this->simulationPool = SplFixedArray::fromArray($this->fishes);
        for ($i = $forDays; $i > 0; $i--) {
            $newBorns = [];
            foreach ($this->simulationPool as $fish) {
                $newBornOrNull = $fish->doAge();
                if ($newBornOrNull !== null) {
                    $newBorns[] = $newBornOrNull;
                }
            }
            $index = count($this->simulationPool);
            $this->simulationPool->setSize(count($this->simulationPool) + count($newBorns));
            foreach ($newBorns as $fish) {
                $this->simulationPool[$index++] = $fish;
            }
        }
        $count = count($this->simulationPool);
        $this->simulationPool = new SplFixedArray(0);
        return $count;
    }
}

class Fish
{
    public function __construct(private int $age) { }

    public function getAge(): int
    {
        return $this->age;
    }

    public function doAge(): Fish|null
    {
        if ($this->age === 0) {
            $this->age = 6;
            return new Fish(8);
        }
        $this->age = $this->age - 1;
        return null;
    }
}