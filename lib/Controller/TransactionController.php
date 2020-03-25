<?php

namespace OCA\Twigs\Controller;

use DateTime;
use OCA\Twigs\Db\BudgetMapper;
use OCA\Twigs\Db\Budget;
use OCA\Twigs\Db\CategoryMapper;
use OCA\Twigs\Db\Category;
use OCA\Twigs\Db\TransactionMapper;
use OCA\Twigs\Db\Transaction;
use OCA\Twigs\Db\UserPermissionMapper;
use OCA\Twigs\Db\UserPermission;
use OCP\IRequest;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Controller;
use \OCP\ILogger;
use OCP\AppFramework\Http;

class TransactionController extends Controller
{
	private $userId;
	private $budgetMapper;
	private $categoryMapper;
	private $transactionMapper;
	private $userPermissionMapper;
	private $logger;
	private const DATE_FORMAT = "Y-m-d\TH:i:s.v\Z";
    private const AMOUNT_REGEX = "/^(([\d]{1,3}[\,\.]?)?([\d]{3}([\.\,])?)+([\.\,][\d]{2})?|[\d]+)$/";

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
	public function index(?int $budgetId, ?int $categoryId, ?int $count)
	{
		try {
			if ($budgetId != null) {
				$this->userPermissionMapper->find($budgetId, $this->userId);
			} else if ($categoryId != null) {
				$category = $this->categoryMapper->find($categoryId);
				$budgetId = $category->getBudgetId();
				$this->userPermissionMapper->find($budgetId, $this->userId);
			} else {
				return new DataResponse([], Http::STATUS_BAD_REQUEST);
			}
			return new DataResponse($this->transactionMapper->findAll($budgetId, $categoryId, $count));
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
			$transaction = $this->transactionMapper->find($id);
			$this->userPermissionMapper->find($transaction->getBudgetId(), $this->userId);
			return new DataResponse($transaction);
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
	 * @param string date
	 * @param bool expense
	 * @param int categoryId
	 * @param int budgetId
	 */
	public function create(
		string $name,
		?string $description,
		int $amount,
		string $date,
		bool $expense,
		int $categoryId,
		int $budgetId
	) {
		try {
			$userPermission = $this->userPermissionMapper->find($budgetId, $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		$transaction = new Transaction();
		$transaction->setName($name);
		$transaction->setDescription($description);
		$transaction->setAmount($amount);
		$transaction->setExpense((int) $expense);
		$dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $date);
		if (!$dateTime) {
			return new DataResponse(["message" => "Invalid date format: '$date'"], Http::STATUS_BAD_REQUEST);
		}
		$transaction->setDate($dateTime->getTimestamp());
		$this->logger->error("Setting category $categoryId for new transaction");
		try {
			$category = $this->categoryMapper->find((int) $categoryId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		$transaction->setCategoryId($categoryId);
		if ($category->getBudgetId() === $budgetId) {
			$transaction->setCategoryId($categoryId);
		}
		$transaction->setBudgetId($budgetId);
		$transaction->setCreatedBy($this->userId);
		$transaction->setCreatedDate(time());
		return new DataResponse($this->transactionMapper->insert($transaction));
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
	public function update(
		int $id,
		string $name,
		string $description,
		int $amount,
		string $date,
		bool $expense,
		int $categoryId,
		int $budgetId
	) {
		try {
			$transaction = $this->transactionMapper->find($id);
			$userPermission = $this->userPermissionMapper->find($budgetId, $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		$transaction->setName($name);
		$transaction->setDescription($description);
		$transaction->setAmount($amount);
		$transaction->setExpense((int) $expense);
		$dateTime = DateTime::createFromFormat(self::DATE_FORMAT, $date);
		if (!$dateTime) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		$transaction->setDate($dateTime->getTimestamp());
		$transaction->setBudgetId($budgetId);
		$transaction->setUpdatedBy($this->userId);
		$transaction->setUpdatedDate(time());
		try {
			$userPermission = $this->userPermissionMapper->find($budgetId, $this->userId);
			if ($userPermission->getPermission() >= UserPermission::PERMISSION_WRITE) {
				$transaction->setBudgetId($budgetId);
			}
		} catch (Exception $e) {
		}
		$category = $this->categoryMapper->find($categoryId);
		if ($category->getBudgetId() === $budgetId) {
			$transaction->setCategoryId($categoryId);
		}
		return new DataResponse($this->transactionMapper->update($transaction));
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
			$transaction = $this->transactionMapper->find($id);
			$userPermission = $this->userPermissionMapper->find($transaction->getBudgetId(), $this->userId);
		} catch (Exception $e) {
			return new DataResponse([], Http::STATUS_NOT_FOUND);
		}
		if ($userPermission->getPermission() < UserPermission::PERMISSION_WRITE) {
			return new DataResponse([], Http::STATUS_FORBIDDEN);
		}
		return new DataResponse($this->transactionMapper->delete($transaction));
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param int $budgetId
	 * @param int $categoryId
	 * @param string $startDate
	 * @param string $endDate
	 */
	public function sum(
		?int $budgetId,
		?int $categoryId,
		?string $startDate,
		?string $endDate
	) {
		$startDateTime = null;
		if ($startDate === null) {
			$startDateTime = new DateTime();
			$startDateTime->setDate(
				$startDateTime->format('Y'),
				$startDateTime->format('m'),
				1
			);
			$startDateTime->setTime(0, 0, 0, 0);
		} else {
			$startDateTime = DateTime::createFromFormat(self::DATE_FORMAT, $startDate);
		}
		if (!$startDateTime) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		$endDateTime = null;
		if ($endDate === null) {
			$endDateTime = new DateTime();
			$endDateTime->setDate(
				$endDateTime->format('Y'),
				$endDateTime->format('m'),
				$endDateTime->format('t')
			);
			$endDateTime->setTime(23, 59, 59, 999);
		} else {
			$endDateTime = DateTime::createFromFormat(self::DATE_FORMAT, $endDate);
		}
		if (!$endDateTime) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		if ($budgetId != null) {
			try {
				$this->userPermissionMapper->find($budgetId, $this->userId);
			} catch (Exception $e) {
				return new DataResponse([], Http::STATUS_NOT_FOUND);
			}
			return new DataResponse([
				'budgetId' => $budgetId,
				'sum' => $this->transactionMapper->sumByBudgetId(
					$budgetId,
					$startDateTime->getTimestamp(),
					$endDateTime->getTimestamp()
				)
			], Http::STATUS_OK);
		}
		if ($categoryId != null) {
			try {
				$category = $this->categoryMapper->find($categoryId);
				$this->userPermissionMapper->find($category->getBudgetId(), $this->userId);
			} catch (Exception $e) {
				return new DataResponse([], Http::STATUS_NOT_FOUND);
			}
			return new DataResponse([
				'categoryId' => $categoryId,
				'sum' => $this->transactionMapper->sumByCategoryId(
					$categoryId,
					$startDateTime->getTimestamp(),
					$endDateTime->getTimestamp()
				)
			], Http::STATUS_OK);
		}
		return new DataResponse([], Http::STATUS_BAD_REQUEST);
	}
}
