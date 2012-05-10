
		<div id="<?php e($this->output->target) ?>">
			<?php foreach($this->output->list as $k => $list): ?>

				<div data-type="<?php e($k) ?>">

					<?php if (empty($list)): ?>
						<div class="error">No <?php e(ucfirst($k)) ?></div>
					<?php else: ?>
						<div class="result"><?php e(ucfirst($k)) ?></div>
						<?php foreach((array)$list as $v): ?>
							<div><?php e($v) ?></div>
						<?php endforeach; ?>
					<?php endif; ?>

				</div>

			<?php endforeach; ?>
		</div>

		<script>

			(function($){

				$('select[data-item]')
					.live('change', function(){
						var _this = $(this);

						var div = $('#' + _this.attr('data-target') + ' > div[data-type]').show();
						var type = _this.val();

						if (type != 'all')
						{
							div.filter(':not([data-type="' + _this.val() + '"])').hide();
						}
					})
					.trigger('change')
				;

			})(jQuery);

		</script>