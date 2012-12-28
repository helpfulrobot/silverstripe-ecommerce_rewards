<?php

class EcommerceRewardsOrderDecorator extends DataObjectDecorator {

	public function extraStatics() {
		return array (
			'db' => array (
				'PointsTotal' => 'Currency',
				'RewardsTotal' => 'Currency',
			),
			'has_many' => array(
				'RewardItems' => 'RewardItem'
			),
		);
	}

	/**
	 *
	 * @return Currency
	 */
	function CalculatePointsTotal(){
		$total = 0;
		$items = $this->owner->Items();
		if($items) {
			foreach($items as $item){
				$total += $item->Product()->PointsValue() * $item->Quantity;
			}
		}
		return $total;
	}

	/**
	 *
	 * @return Currency
	 */
	function CalculateRewardsTotal(){
		$total = 0;
		if($items = $this->owner->RewardItems()){
			foreach($items as $item){
				$total += $item->TotalPoints();
			}
		}
		return $total;
	}

	function updateCMSFields(&$fields){
		$fields->removeByName("RewardItems");
		$fields->removeByName("PointsTotal");
		$fields->removeByName("RewardsTotal");
	}

}

