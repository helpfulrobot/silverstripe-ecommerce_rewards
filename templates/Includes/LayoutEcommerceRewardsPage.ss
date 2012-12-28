<% if CalculateOrders %>
	<% if NeedsCalculation %>
		<p class="message warning">
		Your points still need to be updated.
		</p>
	<% else %>
		<table summary="Points">
			<thead>
				<tr>
					<th scope="col">Order</th>
					<th scope="col">Added</th>
					<th scope="col">Deducted</th>
					<th scope="col">Change</th>
					<th scope="col">Running Total</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th scope="row" colspan="4">Total</th>
					<th scope="col">$Total</th>
				</tr>
			</tfoot>
			<tbody>
				<% control CalculateOrders %>
				<tr>
					<td>$Order.Title</td>
					<td>$Order.PointsTotal</td>
					<td>$Order.RewardsTotal</td>
					<td>$Order.PointChange</td>
					<td>$Order.RunningTotal</td>
				</tr>
				<% end_control %>
			</tbody>
		</table>
	<% end_if %>
<% else %>
	<p class="message warning">
	You have not collected any points yet.
	</p>
<% end_if %>
