<?php
namespace OCA\Twigs\Db;

use DateTime;
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
    protected $createdDate;
    protected $updatedBy;
    protected $updatedDate;

    public function jsonSerialize() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'amount' => (int) $this->amount,
            'date' => $this->formatDate($this->date),
            'expense' => (bool) $this->expense,
            'categoryId' => (int) $this->categoryId,
            'budgetId' => (int) $this->budgetId,
            'createdBy' => $this->createdBy,
            'createdDate' => $this->formatDate($this->createdDate),
            'updatedBy' => $this->updatedBy,
            'updatedDate' => $this->formatDate($this->updatedDate),
        ];
    }

    private function formatDate(?int $timestamp): ?string {
        if (!$timestamp) return null;
        $datetime = new DateTime();
        $datetime->setTimestamp($timestamp);
        return $datetime->format(DateTime::ATOM);
    }
}