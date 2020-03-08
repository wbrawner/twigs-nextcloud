<?php
namespace OCA\Twigs\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Budget extends Entity implements JsonSerializable {

    protected $name;
    protected $description;
    protected $users = [];

    public function jsonSerialize() {
        $users = [];
        foreach ($this->users as $user) {
            array_push(
                $users,
                $user->jsonSerialize()
            );
        }
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'users' => $users
        ];
    }
}