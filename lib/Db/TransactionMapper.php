<?php

namespace OCA\Twigs\Db;

use Doctrine\DBAL\FetchMode;
use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class TransactionMapper extends QBMapper
{

    public static $TABLE_NAME = 'twigs_transactions';

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, TransactionMapper::$TABLE_NAME, Transaction::class);
    }

    public function find(int $id)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('id', $qb->createNamedParameter($id))
            );

        return $this->findEntity($qb);
    }

    public function findAll(int $budgetId, ?int $categoryId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('budget_id', $qb->createNamedParameter($budgetId))
            );

        if ($categoryId) {
            $qb->andWhere(
                $qb->expr()->eq('category_id', $qb->createNamedParameter($categoryId))
            );
        }

        return $this->findEntities($qb);
    }

    public function deleteAll(int $budgetId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('budget_id', $budgetId);
        return $qb->execute();
    }

    public function sumByBudgetId(int $budgetId, int $startDate, int $endDate)
    {
        $sql = <<<EOD
        SELECT (COALESCE((
                SELECT SUM(amount) 
                FROM `*PREFIX*twigs_transactions`
                WHERE budget_id = ?
                AND expense = 0
                AND date >= ?
                AND date <= ?
            ), 0)) - (COALESCE((
                SELECT SUM(amount) 
                FROM `*PREFIX*twigs_transactions`
                WHERE budget_id = ?
                AND expense = 1
                AND date >= ?
                AND date <= ?
            ), 0));
        EOD;
        $statement = $this->db->prepare($sql);
        $statement->bindParam(1, $budgetId);
        $statement->bindParam(2, $startDate);
        $statement->bindParam(3, $endDate);
        $statement->bindParam(4, $budgetId);
        $statement->bindParam(5, $startDate);
        $statement->bindParam(6, $endDate);
        $statement->execute();
        return (int) $statement->fetch(FetchMode::COLUMN);
    }

    public function sumByCategoryId(int $categoryId, int $startDate, int $endDate)
    {
        $sql = <<<EOD
        SELECT (COALESCE((
                SELECT SUM(amount) 
                FROM `*PREFIX*twigs_transactions`
                WHERE category_id = ?
                AND expense = 0
                AND date >= ?
                AND date <= ?
            ), 0)) - (COALESCE((
                SELECT SUM(amount) 
                FROM `*PREFIX*twigs_transactions`
                WHERE category_id = ?
                AND expense = 1
                AND date >= ?
                AND date <= ?
            ), 0));
        EOD;
        $statement = $this->db->prepare($sql);
        $statement->bindParam(1, $categoryId);
        $statement->bindParam(2, $startDate);
        $statement->bindParam(3, $endDate);
        $statement->bindParam(4, $categoryId);
        $statement->bindParam(5, $startDate);
        $statement->bindParam(6, $endDate);
        $statement->execute();
        return (int) $statement->fetch(FetchMode::COLUMN);
    }
}
