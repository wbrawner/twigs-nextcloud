<?php

namespace OCA\Twigs\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class CategoryMapper extends QBMapper
{

    public static $tableName = 'twigs_categories';
    protected $userPermissionMapper;

    public function __construct(IDbConnection $db, UserPermissionMapper $userPermissionMapper)
    {
        parent::__construct($db, CategoryMapper::$tableName, Category::class);
        $this->userPermissionMapper = $userPermissionMapper;
    }

    public function find(int $id, string $userId)
    {
        $userPermission = $this->userPermissionMapper->find($id, $userId);
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->eq('id', $qb->createNamedParameter($userPermission->budgetId))
            );

        return $this->findEntity($qb);
    }

    public function findAll(string $userId)
    {
        $userPermissions = $this->userPermissionMapper->findAll($userId);
        $budgets = [];
        foreach ($userPermissions as $userPermission) {
            array_push($budgets, $userPermission->budgetId);
        }
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->in('id', $budgets)
            );

        return $this->findEntities($qb);
    }

    public function save(Category $category)
    {
        return $this->insertOrUpdate($category);
    }

    public function delete(int $categoryId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('id', $categoryId);
        return $qb->execute();
    }

    public function deleteAll(int $budgetId)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('budget_id', $budgetId);
        return $qb->execute();
    }
}
