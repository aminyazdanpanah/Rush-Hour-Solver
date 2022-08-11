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

use JetBrains\PhpStorm\Pure;
use RushHourSolver\Enums\Direction;
use RushHourSolver\Enums\Orientation;

class BoardSolver
{
    /**
     * @var Board
     */
    private Board $board;

    /**
     * @param Board $board
     */
    public function __construct(Board $board)
    {
        $this->board = $board;
    }

    /**
     * @return array|null
     * @throws \Exception
     */
    public function getSolution(): ?array
    {
        $matrix = $this->board->getMatrix();
        $visited_matrix = [];
        $stack = [[[], $matrix]];
        $num = 0;

        while (count($stack) > 0) {
            list($movements, $matrix) = $stack[$num];
            $num++;
            if ($this->isSolved($matrix)) {
                return $movements;
            }

            $all_states = $this->getAllStates($matrix);
            foreach ($all_states as $state) {
                list($new_movements, $new_matrix) = $state;
                $matrix_hash = sha1(serialize($new_matrix));
                if (!in_array($matrix_hash, $visited_matrix)) {
                    $stack[] = [array_merge($movements, $new_movements), $new_matrix];
                    $visited_matrix[] = $matrix_hash;
                }
            }
        }

        return null;
    }

    /**
     * @param array $matrix
     * @return bool
     */
    #[Pure] private function isSolved(array $matrix): bool
    {
        for ($i = 0; $i < $this->board->getHeight(); $i++) {
            for ($j = 0; $j < $this->board->getWidth(); $j++) {
                $vehicle = $matrix[$j][$i];
                if ($vehicle instanceof Vehicle && $vehicle->isMainVehicle() && $j === $this->board->getWidth() - 1) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param array $matrix
     * @return array
     * @throws \Exception
     */
    private function getAllStates(array $matrix): array
    {
        $states = [];
        for ($i = 0; $i < $this->board->getHeight(); $i++) {
            for ($j = 0; $j < $this->board->getWidth(); $j++) {
                $vehicle = $matrix[$j][$i];
                if ($vehicle instanceof Vehicle) {
                    foreach ([Direction::BACKWARD, Direction::FORWARD] as $direction) {
                        if ($this->isMovable($vehicle, $direction, $matrix)) {
                            $new_matrix = $matrix;
                            $new_vehicle = clone $new_matrix[$j][$i];
                            $new_vehicle->move($direction);

                            $old_positions = $vehicle->getAllPositions();
                            $new_positions = $new_vehicle->getAllPositions();

                            $new_matrix = $this->updateVehicle($new_matrix, $new_vehicle, $old_positions, $new_positions);
                            $states[] = [[[$vehicle, $direction]], $new_matrix];
                        }
                    }
                }
            }
        }

        return $states;
    }

    /**
     * @param Vehicle $vehicle
     * @param mixed $direction
     * @param array $matrix
     * @return bool
     * @throws \Exception
     */
    private function isMovable(Vehicle $vehicle, mixed $direction, array $matrix): bool
    {
        $position = $vehicle->getPosition();
        $x = $position["endX"];
        $y = $position["endY"];
        $orientation = $vehicle->getOrientation();
        $movable = false;

        if ($orientation === Orientation::HORIZONTAL && $direction === Direction::FORWARD) {
            $x = $position["endX"] + 1;
            $movable = $x < $this->board->getWidth();
        } elseif ($orientation === Orientation::HORIZONTAL && $direction === Direction::BACKWARD) {
            $x = $position['startX'] - 1;
            $movable = $x > -1;
        } elseif ($orientation === Orientation::VERTICAL && $direction === Direction::FORWARD) {
            $y = $position["endY"] + 1;
            $movable = $y < $this->board->getHeight();
        } elseif ($orientation === Orientation::VERTICAL && $direction === Direction::BACKWARD) {
            $y = $position['startY'] - 1;
            $movable = $y > -1;
        }

        if ($movable) {
            $vehicle = $matrix[$x][$y];
            if ($vehicle instanceof Vehicle) {
                return false;
            }
        } else {
            return false;
        }

        return true;
    }

    /**
     * @param array $matrix
     * @param Vehicle $vehicle
     * @param array $old_positions
     * @param $new_positions
     * @return array
     */
    private function updateVehicle(array $matrix, Vehicle $vehicle, array $old_positions, $new_positions): array
    {
        foreach ($old_positions as $position) {
            $x = $position["x"];
            $y = $position["y"];
            $matrix[$x][$y] = 0;
        }

        foreach ($new_positions as $position) {
            $x = $position["x"];
            $y = $position["y"];
            $matrix[$x][$y] = $vehicle;
        }

        return $matrix;
    }
}