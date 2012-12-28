<?php

class EcommerceRewardsMemberDecorator extends DataObjectDecorator {
	public function extraStatics() {
		return array (
			'db' => array (
				'PointsBalance' => 'Currency'
			)
		);
	}

	function updateCMSFields(&$fields) {
		$fields->addFieldToTab("Root.Points", new ReadonlyField("PointsBalance"));
	}
}



