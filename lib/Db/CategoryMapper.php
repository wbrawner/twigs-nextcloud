<?php

namespace OCA\Twigs\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class CategoryMapper extends QBMapper
{

    public static $TABLE_NAME = 'twigs_categories';
    protected $userPermissionMapper;

    public function __construct(IDbConnection $db, UserPermissionMapper $userPermissionMapper)
    {
        parent::__construct($db, CategoryMapper::$TABLE_NAME, Category::class);
        $this->userPermissionMapper = $userPermissionMapper;
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

    public function findAll(int $budgetId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('budget_id', $qb->createNamedParameter($budgetId))
            );

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
