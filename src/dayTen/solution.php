<?php
$syntaxChecker = new SyntaxChecker(__DIR__ . '/input');
printf("1st solution: %d \n", $syntaxChecker->run());
printf("2nd solution: %d \n", $syntaxChecker->getAutocompleteScore());

class SyntaxChecker {
    private iterable $lineIterator;
    private array $symbolTable = [
        '(' => ')',
        '[' => ']',
        '{' => '}',
        '<' => '>'
    ];
    private array $scoreTable = [
        ')' => 3,
        ']' => 57,
        '}' => 1197,
        '>' => 25137
    ];
    private array $autoCompleteScoreTable = [
        ')' => 1,
        ']' => 2,
        '}' => 3,
        '>' => 4
    ];
    private array $autoCompleteScores = [];

    public function __construct(string $inputFilePath)
    {
        $this->lineIterator = (function () use ($inputFilePath) {
            $handle = fopen($inputFilePath, 'r');
            while($line = fgets($handle)) {
                yield trim($line);
            }
        })();
    }

    public function run(): int
    {
        $score = 0;
        foreach ($this->lineIterator as $line) {
            $score += $this->checkLine(str_split($line));
        }
        return $score;
    }

    public function getAutocompleteScore(): int
    {
        sort($this->autoCompleteScores);
        $scoresCount = count($this->autoCompleteScores);
        return $this->autoCompleteScores[floor($scoresCount/2)];
    }

    private function checkLine(iterable $characterIterator): int 
    {
        $charBuffer = [];
        foreach ($characterIterator as $char) {
            if ($this->openingChar($char)) {
                $charBuffer[] = $char;
                continue;
            }
            $charToClose = array_pop($charBuffer);
            $score = $this->checkClosingChar($charToClose, $char);
            if ($score > 0) {
                return $score;
            }
        }
        $this->autoCompleteScores[] = $this->calculateAutocompleteScore($charBuffer);
        return 0;
    }

    private function openingChar(string $char): bool
    {
        return in_array($char, ['(', '[', '{', '<'], true);
    }

    private function checkClosingChar(string $opening, string $closing): int
    {
        if ($this->closingCharFor($opening) !== $closing) {
            return $this->scoreTable[$closing];
        }
        return 0;
    }

    private function closingCharFor(string $opening): string
    {
        return $this->symbolTable[$opening];
    }

    private function calculateAutocompleteScore(array $chars): int
    {
        $score = 0;
        while ($char = array_pop($chars)) {
            $closingChar = $this->closingCharFor($char);
            $score = $score * 5 + $this->autoCompleteScoreTable[$closingChar];
        }
        return $score;
    }
}