<?php

$input = file_get_contents(__DIR__ . '/input');
$rows = explode("\n", $input);
$drawnNumbers = array_shift($rows);
$boards = createBoards($rows);

$winningBoards = findWinningBoards($boards, explode(',', $drawnNumbers));
echo '1st Solution: ' . array_sum($boards[$winningBoards[0]]->unmarkedNumbers()) * $boards[$winningBoards[0]]->lastDrawnNumber() . "\n";
echo '2nd Solution: ' . array_sum($boards[$winningBoards[1]]->unmarkedNumbers()) * $boards[$winningBoards[1]]->lastDrawnNumber() . "\n";

/**
 * @param Board[] $boards
 * @param int[] $drawnNumbers
 * @return Board[]
 */
function findWinningBoards(array $boards, array $drawnNumbers): array
{
    $winningBoards = [];
    foreach (array_map('intval', $drawnNumbers) as $number) {
        foreach ($boards as $key => $board) {
            if ($board->markNumber($number) && !in_array($key, $winningBoards)) {
                $winningBoards[] = $key;
            }
        }
    }
    return [$winningBoards[0], end($winningBoards)];
}

/**
 * @param array $rows
 * @return Board[]
 */
function createBoards(array $rows): array
{
    $boards = [];
    $buffer = [];
    foreach ($rows as $key => $row) {
        if ($key%6 === 0) {
            if ($key > 0) { // Starts with an empty row
                $boards[] = new Board($buffer);
            }
            $buffer = [];
            continue;
        }
        
        $buffer[] = array_map('intval', explode(' ', str_replace('  ', ' ', trim($row))));
    }
    return $boards;
}

class Board {
    private array $numbers;
    private array $columns;
    private array $markedNumbers = [];
    private bool $hasWon = false;

    public function __construct(
        private array $rows
    ) {
        $this->createNumbers($rows);
        $this->createColumns();
    }

    private function createNumbers(array $rows): void
    {
        $this->numbers = array_reduce($rows, fn(array $a, array $b) => [...$a,... $b], []);
    }

    private function createColumns(): void
    {
        $this->columns = [
            [], [], [], [], []
        ];
        foreach ($this->numbers as $key => $number) {
            $this->columns[$key%5][] = $number;
        }
    }

    /**
     * @param int $number
     * @return bool Win
     */
    public function markNumber(int $number): bool
    {
        if (!in_array($number, $this->numbers)) {
            return false;
        }
        if (! $this->hasWon) {
            $this->markedNumbers[] = $number;
        }
        return $this->isWinning();
    }

    private function isWinning(): bool
    {
        $this->hasWon = $this->hasWon || 
            (count($this->markedNumbers) >= 5
            && (
                   array_reduce($this->rows, fn($carry, $row) => $carry || $this->isVectorWinning($row), false)
                || array_reduce($this->columns, fn($carry, $column) => $carry || $this->isVectorWinning($column), false)
            ));
        return $this->hasWon;
    }

    private function isVectorWinning(array $vector): bool
    {
        foreach ($vector as $number) {
            if (! in_array($number, $this->markedNumbers)) {
                return false;
            }
        }
        return true;
    }

    public function unmarkedNumbers(): array
    {
        return array_diff($this->numbers, $this->markedNumbers);
    }

    public function lastDrawnNumber(): int
    {
        var_dump($this);
        return end($this->markedNumbers);
    }
}

