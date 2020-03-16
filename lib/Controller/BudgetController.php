<?php

namespace OCA\Twigs\Controller;

use OCA\Twigs\Db\BudgetMapper;
use OCA\Twigs\Db\Budget;
use OCA\Twigs\Db\CategoryMapper;
use OCA\Twigs\Db\TransactionMapper;
use OCA\Twigs\Db\UserPermissionMapper;
use OCA\Twigs\Db\UserPermission;
use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use \OCP\ILogger;
use OCP\AppFramework\Http;

class BudgetController extends Controller
{
	private $userId;
	private $budgetMapper;
	private $categoryMapper;
	private $transactionMapper;
	private $userPermissionMapper;
	private $logger;

	public function __construct(
		$AppName,
		IRequest $request,
		ILogger $logger,
		BudgetMapper $budgetMapper,
		CategoryMapper $categoryMapper,
		TransactionMapper $transactionMapper,
		UserPermissionMapper $userPermissionMapper,
		$UserId
	) {
		parent::__construct($AppName, $request);
		$this->logger = $logger;
		$this->userId = $UserId;
		$this->budgetMapper = $budgetMapper;
		$this->categoryMapper = $categoryMapper;
		$this->transactionMapper = $transactionMapper;
		$this->userPermissionMapper = $userPermissionMapper;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index()
	{
		$userPermissions = $this->userPermissionMapper->findAll($this->userId);
		$budgets = [];
		foreach ($userPermissions as $userPermission) {
			array_push($budgets, $userPermission->getBudgetId());
		}

		return new DataResponse($this->budgetMapper->findAll($budgets));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $id
	 */
	public function show(int $id)
	{
		try {
			$userPermission = $this->userPermissionMapper->find($id, $this->userId);
			return new DataResponse($this->budgetMapper->find($userPermission->getBudgetId()));
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $name
	 * @param string $description
	 * @param array $users
	 */
	public function create(string $name, string $description, array $users)
	{
		$budget = new Budget();
		$budget->setName($name);
		$budget->setDescription($description);
		$budget = $this->budgetMapper->insert($budget);
		$userPermissions = [];
		$users[$this->userId] = UserPermission::PERMISSION_MANAGE;
		foreach ($users as $user => $permission) {
			$userPermission = new UserPermission();
			$userPermission->setBudgetId($budget->getId());
			$userPermission->setUserId($user);
			$userPermission->setPermission($permission);
			$userPermission = $this->userPermissionMapper->insert($userPermission);
			array_push($userPermissions, $userPermission);
		}
		$budget->setUsers($userPermissions);
		return new DataResponse($budget);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $id
	 * @param string $name
	 * @param string $description
	 * @param array $users
	 */
	public function update(int $id, string $name, string $description, array $users)
	{
		try {
			$userPermission = $this->userPermissionMapper->find($id, $this->userId);
			$budget = $this->budgetMapper->find($userPermission->getBudgetId());
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() != UserPermission::PERMISSION_MANAGE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		if ($name) {
			$budget->setName($name);
		}
		if ($description) {
			$budget->setDescription($description);
		}
		$budget = $this->budgetMapper->update($budget);
		if ($users) {
			$this->userPermissionMapper->deleteAll($budget->id);
			$userPermissions = [];
			$users[$this->userId] = UserPermission::PERMISSION_MANAGE;
			foreach ($users as $user => $permission) {
				$userPermission = new UserPermission();
				$userPermission->setBudgetId($budget->getId());
				$userPermission->setUserId($user);
				$userPermission->setPermission($permission);
				array_push($userPermissions, $this->userPermissionMapper->insert($userPermission));
			}
			$budget->setUsers($userPermissions);
		} else {
			$budget->setUsers($userPermissionMapper->findAllByBudgetId($budget->getId()));
		}
		foreach ($users as $user => $permission) {
			$this->logger->error("User: $user Permission: $permission");
		}
		return new DataResponse($budget);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $id
	 */
	public function destroy(int $id)
	{
		try {
			$userPermission = $this->userPermissionMapper->find($id, $this->userId);
			$budget = $this->budgetMapper->find($userPermission->getBudgetId());
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() != UserPermission::PERMISSION_MANAGE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		// Delete all user permissions for this budget
		$this->userPermissionMapper->deleteAll($budget->getId());
		// Delete all transactions for this budget
		$this->transactionMapper->deleteAll($budget->getId());
		// Delete all categories for this budget
		$this->categoryMapper->deleteAll($budget->getId());
		// Finally, delete the budget itself
		$this->budgetMapper->delete($budget);
		return new DataResponse($budget);
	}

	public function stats(int $budgetId) {
		try {
			$userPermission = $this->userPermissionMapper->find($id, $this->userId);
			$budget = $this->budgetMapper->find($userPermission->getBudgetId());
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		
	}
}
