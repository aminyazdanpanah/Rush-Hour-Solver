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

class BoardLoader
{

    /**
     * @var array board array
     */
    private array $board_array = [];

    /**
     * @throws \Exception
     */
    public function __construct(array $board = null)
    {
        if (isset($board)) {
            $this->validate($board);
            $this->board_array = $board;
        }
    }

    /**
     * @return Board
     * @throws \Exception
     */
    public function getBoard(): Board
    {
        $vehicles = [];
        foreach ($this->board_array as $row_key => $row) {
            foreach ($row as $colum_key => $letter) {
                if ($letter === ".") {
                    continue;
                }
                if (!isset($vehicles[$letter])) {
                    $vehicle = new Vehicle($letter);
                    $vehicle->setStartPosition($colum_key, $row_key);
                    $vehicle->MainVehicle($letter === "r");
                    $vehicles[$letter] = $vehicle;
                } else {
                    $vehicle = $vehicles[$letter];
                    $vehicle->setEndPosition($colum_key, $row_key);
                }
            }
        }

        $board_width = count($this->board_array[0]);
        $board_height = count($this->board_array);
        $board = new Board($board_width, $board_height);
        foreach ($vehicles as $vehicle) {
            $board->addVehicle($vehicle);
        }

        return $board;
    }

    /**
     * @throws \Exception
     */
    public function loadBoardFromFile(string $path): void
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            throw new \Exception("File not found in " . $path);
        }

        $board = array_map("str_split", explode(PHP_EOL, $content));
        $this->validate($board);
        $this->board_array = $board;
    }

    /**
     * @param array|null $board
     * @throws \Exception
     */
    private function validate(?array $board): void
    {
        $board_count = count($board);
        if ($board_count === 0) {
            throw new \Exception("Invalid input! it must not be empty");
        }

        $first_row_count = count($board[0]);
        $red_car_count = 0;
        foreach ($board as $row) {
            if (count($row) !== $first_row_count) {
                throw new \Exception("Invalid Input! All rows need to be the same length.");
            }
            foreach ($row as $letter) {
                if (strlen($letter) !== 1) {
                    throw new \Exception("Only one letter is allowed");
                }

                if (preg_match('/[^A-Za-z]/', $letter) && $letter !== ".") {
                    throw new \Exception("Invalid letter. Only letters and \".\" are allowed.");
                }

                if ($letter === "r") {
                    $red_car_count += 1;
                }
            }
        }
        if ($red_car_count === 0) {
            throw new \Exception("There is no main car in the board");
        }
    }
}