<?php
declare(strict_types=1);

namespace App\Domain\Task\TaskAddress;

class TaskAddress implements \JsonSerializable
{
  private $id;

  private $ext_id;

  private $name;

  private $address;

  private $lat;

  private $long;

  private $radius;

  private $deleted;

  private $order;

  private $type;

  private $trip_type;

  private $orders;

  private $contractor;

  public function __construct(
    ?int $id,
    ?string $ext_id,
    ?string $name,
    ?string $address,
    ?float $lat,
    ?float $long,
    ?float $radius,
    ?int $deleted = null,
    ?int $order = null,
    ?string $type = null,
    ?string $trip_type = null,
    ?array $orders = null,
    ?array $contractor = null
  )
  {
    $this->id = $id;
    $this->ext_id = $ext_id;
    $this->name = $name;
    $this->address = $address;
    $this->lat = $lat;
    $this->long = $long;
    $this->radius = $radius;
    $this->deleted = $deleted;
    $this->order = $order;
    $this->type = $type;
    $this->trip_type = $trip_type;
    $this->orders = $orders;
    $this->contractor = $contractor;
  }

  public function jsonSerialize(): array
  {
    $return = [
      'id' => $this->id,
      'ext_id' => $this->ext_id,
      'name' => $this->name,
      'address' => $this->address,
      'lat' => $this->lat,
      'long' => $this->long,
      'radius' => $this->radius
    ];

    if ($this->deleted) $return['deleted'] = $this->deleted;
    if ($this->order) $return['order'] = $this->order;
    if ($this->type) $return['type'] = $this->type;
    if ($this->trip_type) $return['trip_type'] = $this->trip_type;
    if ($this->orders) $return['orders'] = $this->orders;
    if ($this->contractor) $return['contractor'] = $this->contractor;

    return $return;
  }

  public function getId(): ?int
  {
    return $this->id;
  }

  public function getExtId(): string
  {
    return $this->ext_id;
  }

  public function getAddress(): string
  {
    return $this->address;
  }

  public function getLat(): float
  {
    return $this->lat;
  }

  public function getLong(): float
  {
    return $this->long;
  }

  public function getRadius(): float
  {
    return $this->radius;
  }

  public function getDeleted(): int
  {
    return $this->deleted;
  }

  public function getOrder(): ?string
  {
    return $this->order;
  }

  public function getType(): ?string
  {
    return $this->type;
  }

  public function getOrders(): array
  {
    return $this->orders;
  }

  public function getContractor(): array
  {
    return $this->contractor;
  }
}