<?php
namespace OCA\Twigs\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Category extends Entity implements JsonSerializable {

    protected $name;
    protected $amount;
    protected $expense;
    protected $budgetId;

    public function jsonSerialize() {
        return [
            'id' => (int) $this->id,
            'name' => $this->name,
            'amount' => (int) $this->amount,
            'expense' => (bool) $this->expense,
            'budgetId' => (int) $this->budgetId,
        ];
    }
}