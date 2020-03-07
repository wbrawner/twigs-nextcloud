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
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'expense' => $this->expense,
            'budgetId' => $this->budgetId,
        ];
    }
}