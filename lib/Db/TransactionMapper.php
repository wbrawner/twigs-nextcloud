<?php

namespace OCA\Twigs\Db;

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
}
