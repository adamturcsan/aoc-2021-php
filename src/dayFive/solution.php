<?php

$sections = parseInput(__DIR__ . '/input');

$ortogonallyTouchedPoints = array_reduce($sections, fn(array $carry, Section $section) => [...$carry, ...$section->getTouchedPoints()], []);
$weightedOrtogonalPoints = [];
foreach ($ortogonallyTouchedPoints as $point) {
    if (isset($weightedOrtogonalPoints[$point->asString()])) {
        $weightedOrtogonalPoints[$point->asString()]['count']++;
    } else {
        $weightedOrtogonalPoints[$point->asString()] = [
            'point' => $point,
            'count' => 1
        ];
    }
}

$intersections = array_values(array_filter($weightedOrtogonalPoints, fn(array $weightedPoint) => $weightedPoint['count'] > 1));
echo '1st solution: ' . count($intersections) . "\n";
unset($ortogonallyTouchedPoints, $weightedOrtogonalPoints, $intersections);

$points = array_reduce($sections, fn(array $carry, Section $section) => [...$carry, ...$section->getTouchedPoints(true)], []);
$weightedPoints = [];
foreach ($points as $point) {
    if (isset($weightedPoints[$point->asString()])) {
        $weightedPoints[$point->asString()]['count']++;
    } else {
        $weightedPoints[$point->asString()] = [
            'point' => $point,
            'count' => 1
        ];
    }
}

$pointsWithIntersections = array_values(array_filter($weightedPoints, fn(array $weightedPoint) => $weightedPoint['count'] > 1));
echo '2nd solution: ' . count($pointsWithIntersections) . "\n";

function parseInput(string $inputFilePath): array
{
    $sections = [];
    $inputFile = fopen($inputFilePath, 'r');
    while ($line = fgets($inputFile)) {
        [$x1,$y1,$x2,$y2] = sscanf($line, '%d,%d -> %d,%d');
        if ($x1 <= $x2) { // Direction shouldn't matter
            $sections[] = new Section(new Point($x1, $y1), new Point($x2, $y2));
        } else {
            $sections[] = new Section(new Point($x2, $y2), new Point($x1, $y1));
        }
    }
    return $sections;
}

class Section
{
    private Line|Constant|null $line = null;

    public function __construct(
        private Point $start,
        private Point $stop
    ) {
    }

    /**
     * @return Point[]
     */
    public function getTouchedPoints(bool $withDiagonals = false): array
    {
        $line = $this->getLine();
        $points = [];
        if ($line instanceof Constant) {
            foreach (range($this->start->y, $this->stop->y) as $step) {
                $points[] = new Point(
                    $this->start->x,
                    $step
                );
            }
            return $points;
        }
        if (!$withDiagonals && $line->elevation !== .0) {
            return [];
        }
        $i = 0; // Used for keep track of iteration (as would be x normalized to a start from y axis)
        $x = $this->start->x;
        $y = $this->start->y;
        while ($x <= $this->stop->x) {
            if ($x == intval($x) && $y == intval($y)) {
                $points[] = new Point($x, $y);
            }
            $i++;
            $y = $this->start->y + ($line->elevation * $i);
            $x = $this->start->x + $i;
        }
        return $points;
    }

    private function getLine(): Line|Constant
    {
        if ($this->line !== null) {
            return $this->line;
        }
        if ($this->stop->x === $this->start->x) {
            $this->line = new Constant($this->start->x);
            return $this->line;
        }
        $elevation =
            ($this->stop->y - $this->start->y)
            / # ----------------------------
            ($this->stop->x - $this->start->x);
        $offset = $this->start->y - $elevation * $this->start->x; // Where does it intersect the y axis
        $this->line = new Line(
            $elevation,
            $offset
        );
        return $this->line;
    }
}

class Line
{
    public function __construct(
        public float $elevation,
        public float $offset
    ) {
        ;
    }
}

class Constant
{
    public function __construct(
        public int $x
    ) {
        ;
    }
}

class Point
{
    public function __construct(
        public int $x,
        public int $y
    ) {
    }

    public function asString(): string
    {
        return $this->x . ',' . $this->y;
    }

    public function isSame(Point $other): bool
    {
        return $this->x === $other->x && $this->y === $other->y;
    }
}

