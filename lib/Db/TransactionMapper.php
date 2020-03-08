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

    public function find(int $id, string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('id', $qb->createNamedParameter($id))
            )->andWhere(
                $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
            );

        return $this->findEntity($qb);
    }

    public function findAll(string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('user_id', $qb->createNamedParameter($userId))
            );

        return $this->findEntities($qb);
    }

    public function save(Transaction $transaction)
    {
        return $this->insertOrUpdate($transaction);
    }

    public function deleteAll(int $budgetId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('budget_id', $budgetId);
        return $qb->execute();
    }
}
