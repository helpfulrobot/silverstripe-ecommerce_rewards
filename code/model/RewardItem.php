<?php

class RewardItem extends DataObject{

	static $db = array(
		'Quantity' => 'Int',
		'Points' => 'Currency'
	);

	static $has_one = array(
		'Product' => 'Product',
		'Order' => 'Order'
	);

	function QuantityField(){
		return new NumericField('Quantity['.$this->ProductID.']', '', $this->Quantity);
	}

	function TotalPoints(){
		if($this->Points > 0) {
			return $this->Points * $this->Quantity;
		}
		else {
			return $this->Product()->PointsPrice * $this->Quantity;
		}
	}

	function AddRewardItemLink(){
		return $this->Product()->AddRewardItemLink();
	}

	function RemoveRewardItemLink(){
		return $this->Product()->RemoveRewardItemLink();
	}

	function ChangeRewardItemLink(){
		return $this->Product()->ChangeRewardItemLink();
	}

	function onBeforeWrite(){
		parent::onBeforeWrite();
		if(!$this->Points) {
			if($this->ProductID && $this->Product()) {
				$this->Points = $this->Product()->PointsPrice;
			}
		}
	}

}
