<?php
namespace OCA\Twigs\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Transaction extends Entity implements JsonSerializable {

    protected $name;
    protected $description;
    protected $amount;
    protected $date;
    protected $expense;
    protected $categoryId;
    protected $budgetId;
    protected $createdBy;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => $this->amount,
            'date' => $this->date,
            'expense' => $this->expense,
            'categoryId' => $this->categoryId,
            'budgetId' => $this->budgetId,
            'createdBy' => $this->createdBy,
        ];
    }
}