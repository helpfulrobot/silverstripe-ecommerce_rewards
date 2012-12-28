<?php

/**
 *@author nicolaas [at] sunnysideup.co.nz
 *
 *
 **/

class EcommerceRewardsSiteConfigDecorator extends DataObjectDecorator {

	function extraStatics(){
		return array(
			'db' => array(
				'PointsExchangeRate' => 'Currency',
			),
		);
	}


	function updateCMSFields(FieldSet &$fields) {
		$fields->addFieldToTab('Root.RewardPoints', new NumericField('PointsExchangeRate', 'The number of dollars a customer has to spend to receive one reward point.'));
		return $fields;
	}


}
