<?php

//Using a product variation as a reward is not supported at the moment.

class EcommerceRewardsBuyableDecorator extends DataObjectDecorator {

	public function extraStatics() {
		return array (
			'db' => array (
				// The number of points to purchase this product as a reward.
				// If this values is empty then the product cannot be purchased as a reward.
				//'PointsPrice' => 'Currency',
				// The number of points received when this product is purchased.
				// Defaults to points exchange rate specified in the site config if not specified.
				'PointsValue' => 'Currency',
			),
		);
	}

	function updateCMSFields(FieldSet &$fields) {
		//$fields->addFieldToTab('Root.RewardPoints', new NumericField('PointsPrice', 'The number of reward points it costs to purchase this product as a reward. Only products that have a non-zero value will be available as rewards.'));
		if($this instanceOf SiteTree) {
			$fields->addFieldToTab('Root.Content.RewardPoints', new NumericField('PointsValue', 'The number of rewards points received when this product is purchased. This value is optional, the product reward points value will be calculated from the product price if this value is zero.'));
		}
		else {
			$fields->addFieldToTab('Root.RewardPoints', new NumericField('PointsValue', 'The number of rewards points received when this product is purchased. This value is optional, the product reward points value will be calculated from the product price this value is zero.'));
		}
	}

	public function PointsValue(){
		if($this->owner->PointsValue > 0){
			return $this->owner->PointsValue;
		}
		if($this->owner InstanceOf ProductVariation) {
			if($this->owner->Product()->PointsValue!=0){
				return $this->owner->Product()->PointsValue;
			}
		}
		$siteConfig = SiteConfig::current_site_config();
		return round($this->owner->Price/$siteConfig->PointsExchangeRate, 2);
	}

	public function PointsPrice(){
		return 0;
	}

}
