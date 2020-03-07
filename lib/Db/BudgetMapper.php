<?php

namespace OCA\Twigs\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\QBMapper;

class BudgetMapper extends QBMapper
{

    public static $tableName = 'twigs_budgets';
    protected $categoryMapper;
    protected $transactionMapper;
    protected $userPermissionMapper;

    public function __construct(
        IDbConnection $db,
        CategoryMapper $categoryMapper,
        TransactionMapper $transactionMapper,
        UserPermissionMapper $userPermissionMapper
    ) {
        parent::__construct($db, BudgetMapper::$tableName, Budget::class);
        $this->categoryMapper = $categoryMapper;
        $this->transactionMapper = $transactionMapper;
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

    public function save(Budget $budget)
    {
        if ($budget->id) {
            $this->userPermissionMapper->deleteAll($budget->id);
        }
        foreach ($budget->users as $userPermission) {
            $this->userPermissionMapper->save($userPermission);
        }
        return $this->insertOrUpdate($budget);
    }

    public function delete(int $budgetId)
    {
        // Delete all user permissions for this budget
        $this->userPermissionMapper->deleteAll($budgetId);
        // Delete all transactions for this budget
        $this->transactionMapper->deleteAll($budgetId);
        // Delete all categories for this budget
        $this->categorysMapper->deleteAll($budgetId);
        // Finally, delete the budget itself
        $qb = $this->getQueryBuilder();
        $qb->delete($this->getTableName())
            ->where('id', $budgetId);
        return $qb->execute();
    }
}
