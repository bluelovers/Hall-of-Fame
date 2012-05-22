<!-- 転職 -->

<?php if ($this->output->job_change_list): ?>
</form>

	<form action="<?php e(BASE_URL) ?>?char=<?php e($this->output->char_id) ?>" method="post" style="padding:0 15px">
		<h4>JobChange</h4>
		<table>
			<tbody>
				<tr>
					<?php foreach ((array)$this->output->job_change_list as $newjob): ?>
					<td valign="bottom" style="padding:5px 30px;text-align:center"><label> <img src="<?php e($newjob->icon_url()) ?>" />
							<br />
							<input type="radio" value="<?php e($newjob->job()) ?>" name="job" />
							<br />
							<?php e($newjob->job_name()) ?>
						</label></td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
		<input type="submit" class="btn" name="job_change" value="JobChange">
		<input type="hidden" name="job_change" value="1">
	</form>
	<?php endif; ?>
