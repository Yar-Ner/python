<?php
declare(strict_types=1);

namespace App\Domain\User\Request;

class CreateUserRequest
{
    /**
     * @var string
     */
    public $username;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $fullname;

    /**
     * @var array
     */
    public $groupsId;

    /**
     * @var array
     */
    public $rulesId;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string|null
     */
    public $description;

    public static function createFromArray(array $data): CreateUserRequest
    {
        $obj = new self();

        $obj->username = $data['username'];
        $obj->fullname = $data['fullname'];
        $obj->password = $data['password'];
        $obj->status = $data['status'];
        $obj->rulesId = $data['rulesId'] !== '' ? explode(',', $data['rulesId']) : [];
        $obj->groupsId = $data['groupsId'] != '' ? explode(',', $data['groupsId']) : [];
        $obj->description = $data['description'];

        return $obj;
    }
}
