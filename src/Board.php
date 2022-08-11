<?php

/**
 * This file is part of the Rush-Hour-Solver package.
 *
 * (c) Amin Yazdanpanah <contact@aminyazdanpanah.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace RushHourSolver;

class Board
{
    /**
     * @var array
     */
    private array $matrix;

    /**
     * @var int
     */
    private int $width;

    /**
     * @var int
     */
    private int $height;

    /**
     * @param int $width
     * @param int $height
     */
    public function __construct(int $width, int $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->matrix = array_fill(0, $height, array_fill(0, $width, 0));
    }

    /**
     * @throws \Exception
     */
    public function addVehicle(Vehicle $vehicle): void
    {
        $positions = $vehicle->getAllPositions();

        foreach ($positions as $position) {
            $this->matrix[$position["x"]][$position["y"]] = $vehicle;
        }
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return array
     */
    public function getMatrix(): array
    {
        return $this->matrix;
    }
}