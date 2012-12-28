<?php
class OrderStep_UpdateMemberPoints extends OrderStep {

	public static $defaults = array(
		"CustomerCanEdit" => 0,
		"CustomerCanCancel" => 0,
		"CustomerCanPay" => 0,
		"Name" => "Update member points",
		"Code" => "UPDATEPOINTS",
		"Sort" => 27,
		"ShowAsInProcessOrder" => 1,
	);

	/**
	 *@param DataObject $order Order
	 *@return Boolean
	 **/
	public function initStep($order) {
		return TRUE;
	}

	/**
	 * update order points for member
	 * both they points they earned and the points they spent
	 * @param DataObject $order Order
	 * @return Boolean
	 **/
	public function doStep($order) {
		if(!DataObject::get_one("OrderStep_UpdateMemberPoints_Log", "\"OrderID\" = ".$order->ID)) {
			$member = $order->Member();
			$before = $member->PointsBalance;
			$member->PointsBalance += $order->PointsTotal;
			$member->PointsBalance -= $order->RewardsTotal;
			$member->write();
			$log = new OrderStep_UpdateMemberPoints_Log();
			$log->Before = $before;
			$log->PointsTotal = $order->PointsTotal;
			$log->RewardsTotal = $order->RewardsTotal;
			$log->After = $member->PointsBalance;
			$log->OrderID = $order->ID;
			$log->MemberID = $order->MemberID;
			$log->write();
		}
		return TRUE;
	}
}


class OrderStep_UpdateMemberPoints_Log extends OrderStatusLog {

	static $db = array(
		"Before" => "Currency",
		"PointsTotal" => "Currency",
		"RewardsTotal" => "Currency",
		"After" => "Currency"
	);


	function populateDefaults(){
		parent::populateDefaults();
		$this->Title = "Update Member with points from Order.";
		$this->Note = "Records the points before, the points added and subtracted and the points after.";
		$this->InternalUseOnly = 1;
	}
}
