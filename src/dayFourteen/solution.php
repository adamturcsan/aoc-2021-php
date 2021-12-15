<?php

$polymerizer = new Polymerizer(__DIR__ . '/input');

printf("1st answer is: %d \n", $polymerizer->resultFor(10));
printf("2nd answer is: %d \n", $polymerizer->resultFor(40));

class Polymerizer
{
    private array $rules = [];
    private string $template;

    public function __construct($inputFilename)
    {
        $handle = fopen($inputFilename, 'r');
        $this->template = trim(fgets($handle));
        fgets($handle); // Empty line
        while ($line = fgets($handle)) {
            list($if, $then) = explode(' -> ', trim($line));
            $this->rules[$if] = $then;
        }
    }

    public function resultFor(int $steps): int
    {
        $polymerizationState = $this->polymerizeFor($steps);
        $chars = $polymerizationState['chars'];
        sort($chars);
        return end($chars) - $chars[0];
    }

    private function polymerizeFor(int $steps): array
    {
        $polymerizationState = array_reduce(range(1, $steps), fn($carry, $_) => $this->step($carry), [
            'pairs' => array_count_values($this->buildPairs($this->template)),
            'chars' => array_count_values(str_split($this->template))
        ]);
        return $polymerizationState;
    }

    private function step(array $polymerizationState): array
    {
        $pairCounts = $polymerizationState['pairs'];
        $newState = [];
        foreach ($pairCounts as $pair => $count) {
            $result = $this->applyRule(['if' => $pair, 'then' => $this->rules[$pair]]);
            $newPairs = $result['pairs'];

            $newState[$newPairs[0]] = ($newState[$newPairs[0]] ?? 0) + $count;
            $newState[$newPairs[1]] = ($newState[$newPairs[1]] ?? 0) + $count;

            $polymerizationState['chars'][$result['char']] = ($polymerizationState['chars'][$result['char']] ?? 0) + $count;
        }
        $polymerizationState['pairs'] = $newState;
        return $polymerizationState;
    }

    /**
     * @param array $rule
     * @return array New pairs
     */
    private function applyRule(array $rule): array
    {
        $newChar = $rule['then'];
        return [
            'pairs' => [
                str_split($rule['if'])[0] . $newChar,
                $newChar . str_split($rule['if'])[1],
            ],
            'char' => $newChar
        ];
    }

    private function buildPairs(string $template): array
    {
        $pairs = [];
        $prev = null;
        foreach (str_split($template) as $char) {
            if ($prev !== null) {
                $pairs[] = $prev.$char;
            }
            $prev = $char;
        }
        return $pairs;
    }
}