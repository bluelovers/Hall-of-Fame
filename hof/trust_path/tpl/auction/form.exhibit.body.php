
	<?php e($this->slot('auction/form.exhibit.header')); ?>

	<?php if (empty($this->output->select)): ?>

		<?php HOF_Helper_Global::ShowError($this->get('error', 'No items')) ?>

	<?php else: ?>

		<?php e($this->output->select) ?>

		<form action="<?php e(BASE_URL) ?>?menu=auction" method="post">
				<?php e($this->output->show) ?>
				<table>
					<tr>
						<td style="text-align:right">数量(Amount) :</td>
						<td><input type="text" name="Amount" class="text" style="width:60px" value="1" />
							<br /></td>
					</tr>
					<tr>
						<td style="text-align:right">時間(Time) :</td>
						<td><select name="ExhibitTime">
								<option value="24" selected>24 hour</option>
								<option value="18">18 hour</option>
								<option value="12">12 hour</option>
								<option value="6">6 hour</option>
								<option value="3">3 hour</option>
								<option value="1">1 hour</option>
							</select></td>
					</tr>
					<tr>
						<td>開始価格(Start Price) :</td>
						<td><input type="text" name="StartPrice" class="text" style="width:240px" maxlength="10"/>
							<br /></td>
					</tr>
					<tr>
						<td style="text-align:right">コメント(Comment) :</td>
						<td><input type="text" name="Comment" class="text" style="width:240px" maxlength="40"/></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<input type="submit" class="btn" value="Put Auction" name="PutAuction" style="width:240px"/>
							<input type="hidden" name="PutAuction" value="1"/>
							<input type="hidden" name="_timestamp" value="<?php e($this->get('form._timestamp', REQUEST_TIME)) ?>"/>
						</td>
					</tr>
				</table>
			</form>

	<?php endif; ?>