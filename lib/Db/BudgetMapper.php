<?php

namespace OCA\Twigs\Db;

use OCP\IDbConnection;
use OCP\AppFramework\Db\Entity;
use OCP\AppFramework\Db\QBMapper;

class BudgetMapper extends QBMapper
{

    public const TABLE_NAME = 'twigs_budgets';
    protected $categoryMapper;
    protected $transactionMapper;
    protected $userPermissionMapper;

    public function __construct(
        IDbConnection $db,
        CategoryMapper $categoryMapper,
        TransactionMapper $transactionMapper,
        UserPermissionMapper $userPermissionMapper
    ) {
        parent::__construct($db, BudgetMapper::TABLE_NAME, Budget::class);
        $this->categoryMapper = $categoryMapper;
        $this->transactionMapper = $transactionMapper;
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

        $budget = $this->findEntity($qb);
        $budget->setUsers($this->userPermissionMapper->findAllByBudgetId($budget->id));
        return $budget;
    }

    public function findAll(array $budgets)
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from($this->getTableName())
            ->where(
                $qb->expr()->in('id', $budgets)
            );

        $budgets = $this->findEntities($qb);
        foreach ($budgets as $budget) {
            $budget->setUsers($this->userPermissionMapper->findAllByBudgetId($budget->getId()));
        }
        return $budgets;
    }

    public function insert(Entity $entity): Entity {
		$qb = $this->db->getQueryBuilder();
		$qb->insert($this->tableName);
        $qb->setValue('name', $qb->createNamedParameter($entity->getName(), 'string'));
        $qb->setValue('description', $qb->createNamedParameter($entity->getDescription(), 'string'));
		$qb->execute();
        $entity->setId((int)$qb->getLastInsertId());
		return $entity;
    }
    
    public function update(Entity $entity): Entity {
		// if entity wasn't changed it makes no sense to run a db query
        $properties = $entity->getUpdatedFields();
        unset($properties['users']);
		if(\count($properties) === 0) {
			return $entity;
		}

		// entity needs an id
		$id = $entity->getId();
		if($id === null){
			throw new \InvalidArgumentException('Entity which should be updated has no id');
		}

		$qb = $this->db->getQueryBuilder();
		$qb->update($this->tableName);

		// build the fields
		foreach($properties as $property => $updated) {
			$column = $entity->propertyToColumn($property);
			$getter = 'get' . ucfirst($property);
			$value = $entity->$getter();

			$type = $this->getParameterTypeForProperty($entity, $property);
			$qb->set($column, $qb->createNamedParameter($value, $type));
		}

		$idType = $this->getParameterTypeForProperty($entity, 'id');

		$qb->where(
			$qb->expr()->eq('id', $qb->createNamedParameter($id, $idType))
		);
		$qb->execute();

		return $entity;
    }
}
