<?php

namespace App\Model;

use Nette\Utils\DateTime;
use Nextras\Orm\Entity\Entity;
use Nextras\Orm\Relationships\OneHasMany;

/**
 * Debt
 *
 * @property int 					$id 		{primary}
 * @property User|NULL 				$creditor 	{m:1 User::$debtsToGet}
 * @property User|NULL 				$debtor 	{m:1 User::$debtsToPay}
 * @property Currency|NULL 			$currency 	{m:1 Currency, oneSided=true}
 * @property string|NULL			$customFriendName
 * @property string|NULL			$thingName
 * @property float|NULL				$amount
 * @property string|NULL			$note
 * @property DateTime|NULL			$paidAt {default null}
 * @property DateTime|NULL 			$deletedAt {default null}
 * @property DateTime				$createdAt {default now}
 * @property DateTime				$modifiedAt {default now}
 * @property int					$version {default 0}
 * @property int|NULL				$intervalMinutes {default null}
 * @property DateTime|NULL			$intervalRunAt {default null}
 * @property String|NULL 			$intervalType {enum self::INTERVAL_TYPE_*}
 * @property User|NULL				$manager {m:1 User, oneSided=true}
 * @property OneHasMany|Action[]	$actions {1:m Action::$debt}
 */
class Debt extends Entity
{
	const INTERVAL_TYPE_ADD = 'add';
	const INTERVAL_TYPE_CREATE = 'create';

	/**
	 * Converts debt entity to an array, which is suitable for an api response
	 * @return array
	 */
	public function convertToArray() {


		// Convert all nulls to empty strings
		$creditorId = (isset($this->creditor)) ? $this->creditor->id : "";
		$debtorId = (isset($this->debtor)) ? $this->debtor->id : "";

		$customFriendName = (isset($this->customFriendName)) ? $this->customFriendName : "";
		$amount = (isset($this->amount)) ? $this->amount : "";
		$thingName = (isset($this->thingName)) ? $this->thingName : "";
		$note = (isset($this->note)) ? $this->note : "";

		$currencyId = (isset($this->currency)) ? $this->currency->id : "";
		$paidAt = (isset($this->paidAt)) ? $this->paidAt->format('Y-m-d H:i:s') : "";
		$deletedAt = (isset($this->deletedAt)) ? $this->deletedAt->format('Y-m-d H:i:s') : "";

		$managerId = (isset($this->manager->id)) ? $this->manager->id : "";

		$intervalMinutes = (isset($this->intervalMinutes)) ? $this->intervalMinutes : "";
		$intervalRunAt = (isset($this->intervalRunAt)) ? $this->intervalRunAt->format('Y-m-d H:i:s') : "";
		$intervalType = (isset($this->intervalType)) ? $this->intervalType : "";

		return [
			'id' => $this->id,
			'creditorId' => $creditorId,
			'debtorId' => $debtorId,
			'customFriendName' => $customFriendName,
			'amount' => $amount,
			'currencyId' => $currencyId,
			'thingName' => $thingName,
			'note' => $note,
			'paidAt' => $paidAt,
			'deletedAt' => $deletedAt,
			'modifiedAt' => $this->modifiedAt->format('Y-m-d H:i:s'),
			'createdAt' => $this->createdAt->format('Y-m-d H:i:s'),
			'managerId' => $managerId,
			'intervalMinutes' => $intervalMinutes,
			'intervalRunAt' => $intervalRunAt,
			'intervalType' => $intervalType,
			'version' => $this->version
		];
	}
}
