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


use RushHourSolver\Enums\Direction;
use RushHourSolver\Enums\Orientation;

class Vehicle
{

    /**
     * @var string
     */
    private string $name;

    /**
     * @var array
     */
    private array $position = [];

    /**
     * @var bool
     */
    private bool $main_vehicle = false;


    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function setStartPosition(int $x, int $y): void
    {
        $this->position["startX"] = $x;
        $this->position["startY"] = $y;
    }

    public function setEndPosition(int $x, int $y): void
    {
        $this->position["endX"] = $x;
        $this->position["endY"] = $y;
    }

    /**
     * @return array
     */
    public function getPosition(): array
    {
        return $this->position;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getAllPositions(): array
    {
        $positions = [];
        $is_hor = $this->getOrientation() === Orientation::HORIZONTAL;
        $vehicle_length = $is_hor ? $this->position["endX"] - $this->position["startX"] : $this->position["endY"] - $this->position["startY"];

        for ($i = 0; $i < $vehicle_length + 1; $i++) {
            $positions[] = ["x" => $this->position["startX"] + ($is_hor ? $i : 0), "y" => $this->position["startY"] + ($is_hor ? 0 : $i)];
        }

        return $positions;
    }

    /**
     * @return bool
     */
    public function isMainVehicle(): bool
    {
        return $this->main_vehicle;
    }

    /**
     * @param bool $is_main_vehicle
     */
    public function MainVehicle(bool $is_main_vehicle): void
    {
        $this->main_vehicle = $is_main_vehicle;
    }

    /**
     * @return Orientation
     * @throws \Exception
     */
    public function getOrientation(): Orientation
    {
        if ($this->position["startX"] === $this->position["endX"]) {
            return Orientation::VERTICAL;
        } elseif ($this->position["startY"] === $this->position["endY"]){
            return Orientation::HORIZONTAL;
        }else{
            throw new \Exception("Could not get the orientation");
        }
    }

    /**
     * @param Direction $direction
     * @return void
     * @throws \Exception
     */
    public function move(Direction $direction): void
    {
        if($this->getOrientation() === Orientation::HORIZONTAL) {
            $this->position["startX"] = $direction === Direction::FORWARD  ?  $this->position["startX"] + 1 : $this->position["startX"] - 1;
            $this->position["endX"] = $direction === Direction::FORWARD  ?  $this->position["endX"] + 1 : $this->position["endX"] - 1;
        }else {
            $this->position["startY"] = $direction === Direction::FORWARD  ?  $this->position["startY"] + 1 : $this->position["startY"] - 1;
            $this->position["endY"] = $direction === Direction::FORWARD  ?  $this->position["endY"] + 1 : $this->position["endY"] - 1;
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }


}