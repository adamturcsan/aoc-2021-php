<?php

$pool = new OctopusPool(__DIR__ . '/input');

printf("1st solution: %d \n", $pool->simulateFor(100));
printf("2nd solution: %d \n", $pool->simulateForSync());

function deepClone(array $octopuses)
{
    $newPool = [];
    foreach ($octopuses as $rowNum => $row) {
        foreach ($row as $col => $elem) {
            $newPool[$rowNum][$col] = clone $elem;
        }
    }
    return $newPool;
}

class OctopusPool
{
    /**
     *  @var Octopus[][] $octopusesByLine
     */
    private array $octopusesByLine = [];
    /**
     * @var Octopus[][]|null
     */
    private array|null $simulationPool = null;

    public function __construct(string $inputFilePath) {
        $handle = fopen($inputFilePath, 'r');
        while ($line = fgets($handle)) {
            $this->octopusesByLine[] = array_map(
                fn(string $energy) => new Octopus(intval($energy)),
                str_split(trim($line))
            );    
        }
    }

    public function simulateFor(int $steps, bool $shouldDraw = false): int
    {
        $flashes = 0;
        $this->simulationPool = deepClone($this->octopusesByLine);
        if ($shouldDraw) $this->draw();
        foreach (range(1, $steps) as $stepCount) {
            if ($shouldDraw) echo "$stepCount:\n";
            $flashes += $this->simuLateAStep($stepCount);
            if ($shouldDraw) $this->draw(true);
        }
        $this->simulationPool = null;
        return $flashes;
    }

    public function simulateForSync(bool $shouldDraw = false): int
    {
        $this->simulationPool = $this->octopusesByLine;
        if ($shouldDraw) $this->draw();
        $stepCount = 1;
        while ($this->simuLateAStep($stepCount) !== 100) {
            if ($shouldDraw) echo "$stepCount:\n";
            if ($shouldDraw) $this->draw(true);
            $stepCount++;
        }
        $this->simulationPool = null;
        return $stepCount;
    }

    private function simulateAStep(int $stepNumber, array $pool = null): int
    {
        $simulationPool = $pool ?? $this->simulationPool;
        $flashCount = 0;
        $flashes = [];
        foreach ($simulationPool as $row => $octopusLine) {
            foreach ($octopusLine as $col => $octopus) {
                if ($octopus === null) {
                    continue;
                }
                if ($octopus->gainEnergy($stepNumber) === 0) {
                    $flashes[] = [$row, $col];
                }
            }
        }
        $flashCount += count($flashes);
        foreach ($flashes as $flash) {
            $flashCount += $this->simulateAStep($stepNumber, $this->getNeighbours($flash[0], $flash[1]));
        }
        return $flashCount;
    }

    private function getNeighbours(int $row, int $col): array
    {
        return [
            ($row - 1) => [
                ($col - 1) => $this->simulationPool[$row - 1][$col - 1] ?? null,
                ($col)     => $this->simulationPool[$row - 1][$col] ?? null,
                ($col + 1) => $this->simulationPool[$row - 1][$col + 1] ?? null,
            ],
            ($row) => [
                ($col - 1) => $this->simulationPool[$row][$col - 1] ?? null,
                ($col + 1) => $this->simulationPool[$row][$col + 1] ?? null,
            ],
            ($row + 1) => [
                ($col - 1) => $this->simulationPool[$row + 1][$col - 1] ?? null,
                ($col)     => $this->simulationPool[$row + 1][$col] ?? null,
                ($col + 1) => $this->simulationPool[$row + 1][$col + 1] ?? null,
            ],
        ];
    }

    private function draw(bool $simulation = false): void
    {
        foreach (($simulation ? $this->octopusesByLine : $this->simulationPool) as $line) {
            foreach ($line as $octopus) {
                echo $octopus->asString();
            }
            echo "\n";
        }
        echo "\n";
    }
}

class Octopus
{
    private $flashes = [];
    public function __construct(private int $energyLevel) { }

    /**
     * @return int New level
     */
    public function gainEnergy(int $step): int
    {
        if (isset($this->flashes[$step])) {
            return -1;
        }
        if (++$this->energyLevel > 9) {
            $this->energyLevel = 0;
            $this->flashes[$step] = true;
        }
        return $this->energyLevel;
    }

    public function asString(): string
    {
        return strval($this->energyLevel);
    }
}