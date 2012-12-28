<?php


class EcommerceRewardsPage extends Page {

	function IsEcommercePage(){
		return true;
	}

}

class EcommerceRewardsPage_Controller extends Page_Controller{

	static $calculate_orders = null;

	protected $total = 0;

	protected $needsCalculation = true;

	function CalculateOrders(){
		if(self::$calculate_orders == null) {
			//remove null value so we know it has been run.
			self::$calculate_orders = 0;
			//get current member
			$member = Member::currentUser();
			if($member) {
				$orders = DataObject::get(
					"Order",
					"\"MemberID\" = ".$member->ID." AND \"CancelledByID\" = 0 OR \"CancelledByID\" IS NULL",
					" \"Order\".\"ID\" ASC"
				);
				$memberRunningTotal = 0;
				if($orders) {
					//new we can set it to an Array List
					self::$calculate_orders = new DataObjectSet();
					foreach($orders as $order) {
						if($order->IsSubmitted()) {
							//calculate
							$change = $order->PointsTotal - $order->RewardsTotal;
							$memberRunningTotal += $change;
							//add values to Order
							$order->PointChange = $change;
							$order->RunningTotal = $memberRunningTotal;
							//add to list
							self::$calculate_orders->push($order);
						}
					}
					if($member->PointsBalance == $memberRunningTotal) {
						$this->needsCalculation = false;
						$this->total = $memberRunningTotal;
					}
				}
			}
		}
		return self::$calculate_orders;
	}

	function Total(){
		$this->CalculateOrders();
		return $this->total;
	}

	function NeedsCalculation(){
		$this->CalculateOrders();
		return $this->needsCalculation;
	}



}

