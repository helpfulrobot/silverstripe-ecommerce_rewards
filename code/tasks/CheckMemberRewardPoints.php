<?php


class CheckMemberRewardPoints extends BuildTask {

	function getTitle() {
		return 'Check points for all members';
	}

	function getDescription() {
		return 'Goes through all customers and checks their orders and the points gained / points used.';
	}

	function run($request){
		if($request->Param("ID") =="reset") {
			$reset = true;
			echo "<h1 style=\"color: red\">RESETTING MEMBER POINTS!</h1>";
		}
		else {
			$reset = false;
			echo "<h1>MORE OPTIONS</h1>";
			echo "<a href=\"reset/\">Reset points for all members</a>";
		}
		$members = DataObject::get("Member", "", "Member.Email", "INNER JOIN \"Order\" ON \"Order\".\"MemberID\" = \"Member\".\"ID\"");
		foreach($members as $member) {
			echo "
			<h3>$member->FirstName $member->Surname, $member->Email: $member->PointsBalance</h3>
			<style>
				td {text-align: right; font-size: 10px;}
				th {font-size: 10px}
			</style>
			<table border=\"1\">
				<thead>
					<tr>
						<th scope=\"col\" style=\"width: 30em\">ORDER</th>
						<th scope=\"col\" style=\"width: 5em;\">GAINED</th>
						<th scope=\"col\" style=\"width: 5em;\">USED</th>
						<th scope=\"col\" style=\"width: 5em;\">CHANGE</th>
						<th scope=\"col\" style=\"width: 5em;\">RUNNING TOTAL</th>
						<th scope=\"col\" style=\"width: 60em;\">NOTES</th>
					</tr>
				</thead>
				<tbody>";
			$orders = DataObject::get(
				"Order",
				"\"MemberID\" = ".$member->ID." AND \"CancelledByID\" = 0 OR \"CancelledByID\" IS NULL",
				" \"Order\".\"ID\" ASC"
			);
			$memberTotal = 0;
			$sumPointsTotal = 0;
			$sumRewardsTotal = 0;
			if($reset) {
				$member->PointsBalance = 0;
				$member->write();
			}
			if($orders) {
				foreach($orders as $order) {
					if($order->IsSubmitted()) {
						$note = "&nbsp;";
						if(round($order->PointsTotal, 2) != round($order->CalculatePointsTotal(), 2)) {
							$note .= "ERROR, CALCULATED POINTS ADDED: ".$order->CalculatePointsTotal().", difference: ".($order->PointsTotal - $order->CalculatePointsTotal());
							if($order->PointsTotal == 0 && $order->CalculatePointsTotal() > 0) {
								$order->PointsTotal = $order->CalculatePointsTotal();
								//DB::query("UPDATE \"Order\" SET \"PointsTotal\" = ".$order->CalculateRewardsTotal(). " WHERE \"Order\".\"ID\" = ".$order->ID);
								//$order->write();
							}
						}
						if(round($order->RewardsTotal, 2) != round($order->CalculateRewardsTotal(), 2)) {
							$note .= "ERROR, CALCULATED POINTS USED: ".$order->CalculateRewardsTotal().", difference: ".($order->RewardsTotal - $order->CalculateRewardsTotal());
							if($order->RewardsTotal == 0 && $order->CalculateRewardsTotal() > 0) {
								$order->RewardsTotal = $order->CalculateRewardsTotal();
								DB::query("UPDATE \"Order\" SET \"RewardsTotal\" = ".$order->CalculateRewardsTotal(). " WHERE \"Order\".\"ID\" = ".$order->ID);
								//$order->write();
							}
						}
						$change = $order->PointsTotal - $order->RewardsTotal;
						$sumPointsTotal += $order->PointsTotal;
						$sumRewardsTotal += $order->RewardsTotal;
						$memberTotal += $change;
						echo "
					<tr>
						<td>#".$order->ID." ".$order->LastEdited."</td>
						<td>".$order->PointsTotal."</td>
						<td>".$order->RewardsTotal."</td>
						<td>".$change."</td>
						<td>".$memberTotal."</td>
						<td>".$note."</td>
					</tr>";
					}
				}
				$note = "&nbsp;";
				$difference = 0;
				if($member->PointsBalance != $memberTotal) {
					$difference = $member->PointsBalance - $memberTotal;
					$note = "ERROR IN POINTS RECORDED (".$member->PointsBalance.") AND CALCULATED (".$memberTotal."), difference ".$difference;
				}
				echo "
					<tr>
						<th scope=\"col\">TODAY:</th>
						<td><strong>$sumPointsTotal</strong></td>
						<td><strong>$sumRewardsTotal</strong></td>
						<td><strong>".($sumPointsTotal - $sumRewardsTotal)."</strong></td>
						<td><strong>$memberTotal</strong></td>
						<td><strong>$note</strong></td>
					</tr>";
			}
			echo "</tbody></table>";
			if($reset) {
				$member->PointsBalance = $memberTotal;
				$member->write();
			}
		}
	}

}



class CheckMemberRewardPoints_AdminEXT extends Extension {

	static $allowed_actions = array('checkmemberrewardpoints' => true);

	function updateEcommerceDevMenuRegularMaintenance($tasks) {
		$tasks[] = 'checkmemberrewardpoints';
		return $tasks;
	}

	function checkmemberrewardpoints($request) {
		$this->owner->runTask("CheckMemberRewardPoints", $request);
	}

}
