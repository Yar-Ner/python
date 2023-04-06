<?php
declare(strict_types=1);

namespace App\Domain\User\Rule;

use App\Domain\User\Rule\Request\CreateRuleRequest;
use App\Domain\User\Rule\Request\UpdateRuleRequest;

class Rule implements \JsonSerializable
{
    /** @var int|null */
    private $id;

    /** @var string */
    private $name;

    /** @var string|null */
    private $description;

    /** @var string */
    private $handle;

    /** @var \DateTime|null */
    private $updated;

    public function __construct(?int $id, string $name, string $handle, ?string $description = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->handle = $handle;
        $this->description = $description;

        if ($id) {
            $this->updated = new \DateTime();
        }
    }


    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "handle" => $this->handle,
            "description" => $this->description,
            "updated" => $this->updated ? $this->updated->format('Y-m-d в H:i:s') : $this->updated,
        ];
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getHandle(): string
    {
        return $this->handle;
    }

    public function getUpdated(): ?\DateTime
    {
        return $this->updated;
    }

    public static function createFromRequest(CreateRuleRequest $request): Rule
    {
        return new Rule(null, $request->name, $request->handle, $request->description);
    }

    public function updateFromRequest(UpdateRuleRequest $request): void
    {
        $this->name = $request->name ?? $this->name;
        $this->handle = $request->handle ?? $this->handle;
        $this->description = $request->description ?? $this->description;

        $this->updated = new \DateTime();
    }
}
