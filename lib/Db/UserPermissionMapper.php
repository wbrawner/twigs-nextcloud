<?php

namespace OCA\Twigs\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class UserPermissionMapper extends QBMapper
{
    public static $tableName = 'twigs_user_permissions';

    public function __construct(IDbConnection $db)
    {
        parent::__construct($db, UserPermissionMapper::$tableName, UserPermission::class);
    }

    public function find(int $budgetId, string $userId)
    {
        $qb = $this->db->getQueryBuilder();

        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('budget_id', $qb->createNamedParameter($budgetId))
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

    public function save(UserPermission $userPermission)
    {
        return $this->insertOrUpdate($userPermission);
    }

    public function deleteAll(int $budgetId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('budget_id', $budgetId);
        return $qb->execute();
    }
}
