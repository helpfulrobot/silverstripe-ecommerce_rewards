<?php


class OrderRewardsForm extends Form{

	function __construct($controller) {
		$fields = new FieldSet();
		$actions = new FieldSet(
			new FormAction('submit', 'Get It!')
		);
		parent::__construct($controller, 'OrderRewardsForm', $fields, $actions);
	}

	function forTemplate() {
		return $this->renderWith(array(
			$this->class,
			'Form'
		));
	}

	function RewardItems(){
		return $this->controller->RewardItems();
	}

	function RewardsTotalPoints(){
		return $this->controller->RewardsTotalPoints();
	}

	//called on save and skip
	function submit($data, $form) {
		// if rewards added and get it button clicked then validate and save to order object
		if(isset($data['action_submit']) && isset($data['Quantity'])){
			Session::clear($this->controller->RewardsSessionKey());
			foreach($data['Quantity'] as $ProductID => $quantity){
				$item = $this->controller->newReward($ProductID, $quantity);
				Session::set($this->controller->RewardsSessionKey($ProductID), serialize($item));
			}
			if($this->controller->RewardsTotalPoints()>Page_Controller::MemberPointsBalance()){
				$this->sessionMessage('You do not have enough points to purchase these rewards.', 'error');
				Director::redirectBack();
				return;
			}
			$new_items = $this->controller->RewardItems();
		}

		//delete all existing reward items for this order
		$order_items = $this->controller->Order()->RewardItems();
		foreach($order_items as $o_item){
			$o_item->delete();
		}

		// then flush rewards from session
		Session::clear($this->controller->RewardsSessionKey());

		//then link the reward items to the order
		if(isset($new_items)){
			foreach($new_items as $item){
				$item->write();
			}
		}

		// then redirect to next step
		Director::redirect($this->controller->Link().'checkoutstep/orderconfirmationandpayment/');
	}

}

