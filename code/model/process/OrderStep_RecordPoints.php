<?php
class OrderStep_RecordPoints extends OrderStep {

	public static $defaults = array(
		"CustomerCanEdit" => 0,
		"CustomerCanCancel" => 0,
		"CustomerCanPay" => 1,
		"Name" => "Record reward points",
		"Code" => "RECORDPOINTS",
		"Sort" => 26,
		"ShowAsInProcessOrder" => 1,
		"SendInvoiceToCustomer" => 0
	);

	/**
	 * can run step once order has been submitted.
	 *@param DataObject $order Order
	 *@return Boolean
	 **/
	public function initStep($order) {
		return $order->IsSubmitted();
	}

	/**
	 * record points in order object
	 * or in case this is not selected, it will send a message to the shop admin only
	 * The latter is useful in case the payment does not go through (and no receipt is received).
	 * @param DataObject $order Order
	 * @return Boolean
	 **/
	public function doStep($order) {
		if(!DataObject::get_one("OrderStep_RecordPoints_Log", "\"OrderID\" = ".$order->ID)) {
			if(!$order->PointsTotal) {
				$order->PointsTotal = $order->CalculatePointsTotal();
			}
			$order->RewardsTotal = $order->CalculateRewardsTotal();
			$order->write();
			$log = new OrderStep_RecordPoints_Log();
			$log->PointsTotal = $order->PointsTotal;
			$log->RewardsTotal = $order->RewardsTotal;
			$log->OrderID = $order->ID;
			$log->MemberID = $order->MemberID;
			$log->write();
		}
		return true;
	}
}

class OrderStep_RecordPoints_Log extends OrderStatusLog {

	static $db = array(
		"PointsTotal" => "Currency",
		"RewardsTotal" => "Currency"
	);

	function populateDefaults(){
		parent::populateDefaults();
		$this->Title = "Calculate Points from Order.";
		$this->Note = "Works out the points gained and points used for an Order.";
		$this->InternalUseOnly = 1;
	}

}
