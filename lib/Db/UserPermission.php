<?php
namespace OCA\Twigs\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class UserPermission extends Entity implements JsonSerializable {

    const PERMISSION_READ = 0;
	const PERMISSION_WRITE = 1;
	const PERMISSION_MANAGE = 2;

    protected $budgetId;
    protected $userId;
    protected $permission;

    public function jsonSerialize() {
        return [
            'userId' => $this->userId,
            'permission' => $this->permission
        ];
    }
}