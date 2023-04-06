<?php
declare(strict_types=1);

namespace App\Domain\Distance;


class TaskDistance extends Distance
{
  private $return_distance;

  private $ordersDistance;

  public function __construct(string $id, int $distance, ?int $return_distance, array $ordersDistance)
  {
    parent::__construct($id, $distance);
    $this->return_distance = $return_distance;
    $this->ordersDistance = $ordersDistance;
  }

  public function jsonSerialize(): array
  {
    foreach ($this->ordersDistance as &$order) {
      $order = $order->jsonSerialize();
    }

    return [
      'task_id' => $this->getId(),
      'distance' => $this->getDistance(),
      'return_distance' => $this->return_distance,
      'orders' => $this->ordersDistance
    ];
  }
}