<?php
declare(strict_types=1);

namespace App\Domain\User\Group;


use App\Domain\User\Group\Request\CreateGroupRequest;
use App\Domain\User\Group\Request\UpdateGroupRequest;

class Group implements \JsonSerializable
{
    /** @var int|null */
    private $id;

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var array */
    private $rulesId;

    /** @var array */
    private $usersId;

    public function __construct(?int $id, string $name, ?string $description, array $rulesId, array $usersId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->rulesId = $rulesId;
        $this->usersId = $usersId;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rulesId' => $this->rulesId,
            'usersId' => $this->usersId,
            'description' => $this->description,
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

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getRulesId(): array
    {
        return $this->rulesId;
    }

    public function getUsersId(): array
    {
        return $this->usersId;
    }

    public static function createFromRequest(CreateGroupRequest $request): Group
    {
        return new self(null, $request->name, $request->description, $request->rulesId, $request->usersId);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function updateFromRequest(UpdateGroupRequest $request): void
    {
        $this->name = $request->name ?? $this->name;
        $this->rulesId = $request->rulesId ?? $this->rulesId;
        $this->usersId = $request->usersId ?? $this->usersId;
        $this->description = $request->description ?? $this->description;

        $this->updated = new \DateTime();
    }
}
