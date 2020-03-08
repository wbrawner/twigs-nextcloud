<?php

namespace OCA\Twigs\Controller;

use OCA\Twigs\Db\BudgetMapper;
use OCA\Twigs\Db\Budget;
use OCA\Twigs\Db\CategoryMapper;
use OCA\Twigs\Db\Category;
use OCA\Twigs\Db\TransactionMapper;
use OCA\Twigs\Db\UserPermissionMapper;
use OCA\Twigs\Db\UserPermission;
use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use \OCP\ILogger;
use OCP\AppFramework\Http;

class CategoryController extends Controller
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
		$budgetId = $_GET['budgetId'];
		if ($budgetId == null) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		try {
			$this->userPermissionMapper->find($budgetId, $this->userId);
			return new DataResponse($this->categoryMapper->findAll($budgetId));
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
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
			$category = $this->categoryMapper->find($id);
			$this->userPermissionMapper->find($category->getBudgetId(), $this->userId);
			return new DataResponse($category);
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
	 * @param int amount
	 * @param int amount
	 * @param bool expense
	 */
	public function create(string $name, int $amount, int $budgetId, bool $expense)
	{
		try {
			$userPermission = $this->userPermissionMapper->find($budgetId, $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		$category = new Category();
		$category->setName($name);
		$category->setAmount($amount);
		$category->setExpense($expense);
		$category->setBudgetId($budgetId);
		return new DataResponse($this->categoryMapper->insert($category));
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
	public function update(int $id, string $name, string $description, int $amount, int $budgetId, bool $expense)
	{
		try {
			$category = $this->categoryMapper->find($id);
			$userPermission = $this->userPermissionMapper->find($category->getBudgetId(), $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		if ($name) {
			$category->setName($name);
		}
		if ($description) {
			$category->setDescription($description);
		}
		if ($amount) {
			$category->setAmount($amount);
		}
		if ($expense) {
			$category->setExpense($expense);
		}
		if ($budgetId) {
			try {
				$userPermission = $this->userPermissionMapper->find($budgetId, $this->userId);
				$category->setBudgetId($budgetId);
			} catch (Exception $e) {
			}
		}
		return new DataResponse($this->categoryMapper->update($category));
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
			$category = $this->categoryMapper->find($id);
			$userPermission = $this->userPermissionMapper->find($category->getBudgetId(), $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		return new DataResponse($this->categoryMapper->delete($category));
	}
}
