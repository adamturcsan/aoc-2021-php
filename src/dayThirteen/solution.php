<?php

$paper = new Paper(__DIR__ . '/input');

printf("1st solution: %d \n", count($paper->foldOnce()));
echo "Second solution: \n";
$paper->draw($paper->fold());

class Paper
{
    private array $dots = [];
    private array $instructions = [];

    public function __construct(string $inputFilePath) {
        $handle = fopen($inputFilePath, 'r');
        $instructionsComing = false;
        while ($line = fgets($handle)) {
            if ($line === "\n") {
                $instructionsComing = true;
                continue;
            }
            if ($instructionsComing) {
                list($instruction['axis'], $instruction['value']) = explode('=', str_replace('fold along ', '', trim($line)));
                $this->instructions[] = $instruction;
            } else {
                list($dot['x'], $dot['y']) = array_map(intval(...), explode(',', trim($line)));
                $this->dots[] = $dot;
            }
        }
    }

    public function fold()
    {
        $dots = $this->dots;
        foreach ($this->instructions as $instruction) {
            $dots = $this->foldOnce($dots, $instruction);
        }
        return $dots;
    }

    public function draw(array $dots)
    {
        $maxCol = max(array_column($dots, 'x'));
        $maxRow = max(array_column($dots, 'y'));
        $hasDot = fn(int $col, int $row) => in_array(['x' => $col, 'y' => $row], $dots);
        foreach (range(0, $maxRow) as $row) {
            foreach (range(0, $maxCol) as $col) {
                echo $hasDot($col, $row) ? '#' : '.';
            }
            echo "\n";
        }
    }

    public function foldOnce(array $dots = null, $instruction = null): array
    {
        if ($dots === null) {
            $dots = $this->dots;
        }
        if ($instruction === null) {
            $instruction = $this->instructions[0];
        }
        $axisToLeave = $instruction['axis'] === 'x' ? 'y' : 'x';
        $axisToModify = $instruction['axis'];
        $foldedDots = array_map(
            fn($dot) => $dot[$axisToModify] > $instruction['value']
                ? [
                    $axisToLeave => $dot[$axisToLeave],
                    $axisToModify => $dot[$axisToModify] - 2 * ($dot[$axisToModify] - $instruction['value'])
                ]
                : $dot,
            $dots
        );
        return array_map(unserialize(...), array_unique(array_map(fn($dot) => serialize([
            'x' => $dot['x'],
            'y' => $dot['y']
        ]), array_filter($foldedDots))));
    }
}
