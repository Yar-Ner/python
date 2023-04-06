<?php
declare(strict_types=1);

namespace App\Domain\User;

use App\Domain\User\Request\CreateUserRequest;
use App\Domain\User\Request\UpdateUserRequest;
use JsonSerializable;

class User implements JsonSerializable
{
    /**
     * @var int|null
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $fullname;

    /**
     * @var array
     */
    private $groupsId;

    /**
     * @var array
     */
    private $rulesId;

    /**
     * @var string
     */
    private $status;

    /**
     * @var string|null
     */
    private $description;

    //todo: Move username, firstname, etc in sub object like UserInfo.
    public function __construct(
        ?int $id,
        string $username,
        string $password,
        string $fullname,
        string $status,
        ?string $description = null,
        array $groupsId = [],
        array $rulesId = []
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->fullname = $fullname;
        $this->groupsId = $groupsId;
        $this->rulesId = $rulesId;
        $this->status = $status;
        $this->description = $description;
        $this->password = $password;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'fullname' => $this->fullname,
            'status' => $this->status,
            'description' => $this->description,
            'groupsId' => $this->groupsId,
            'rulesId' => $this->rulesId,
            'value' => $this->fullname,
        ];
    }

    public function getGroupsId(): array
    {
        return $this->groupsId;
    }

    public function getRulesId(): array
    {
        return $this->rulesId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }


    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getFullname(): string
    {
        return $this->fullname;
    }

    public static function createFromRequest(CreateUserRequest $request): User
    {
        return new User(
            null,
            $request->username,
            $request->password,
            $request->fullname,
            $request->status,
            $request->description,
            $request->groupsId,
            $request->rulesId
        );
    }

    public function UpdateFromRequest(UpdateUserRequest $request): void
    {
        if ($request->username) $this->username = $request->username;
        if ($request->password) $this->password = $request->password;
        if ($request->fullname) $this->fullname = $request->fullname;
        if (isset($request->description)) $this->description = $request->description;
        if ($request->status) $this->status = $request->status;
        if (isset($request->rulesId)) $this->rulesId = $request->rulesId;
        if (isset($request->groupsId)) $this->groupsId = $request->groupsId;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
