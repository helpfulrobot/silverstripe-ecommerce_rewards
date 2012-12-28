<?php


class EcommerceRewardsRewardDecorator extends DataObjectDecorator {


	public function extraStatics() {
		return array (
			'db' => array (
				'PointsPrice' => 'Currency',
			),
		);
	}

	public function updateCMSFields(&$fields) {
		$fields->addFieldToTab('Root.Content.RewardPoints', new NumericField('PointsPrice', 'The number of reward points it costs to purchase this product as a reward. Only products that have a non-zero value will be available as rewards.'));
		return $fields;
	}

}

