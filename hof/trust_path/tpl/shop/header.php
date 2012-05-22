
	<!-- 店ヘッダ -->

	<?php $this->set('npc.talk.title', '店') ?>
	<?php $this->set('npc.talk.no', $this->get('npc_no', 'ori_002')) ?>
	<?php ob_start(); ?>
				いらっしゃいませー<br />
				<a href="<?php e(HOF::url('shop', 'buy')) ?>">買う</a>/<a href="<?php e(HOF::url('shop', 'sell')) ?>">売る</a><br />
				<a href="<?php e(HOF::url('shop', 'work')) ?>">アルバイト</a>
	<?php $content = ob_get_clean(); ?>

	<?php e($this->slot('layout/npc.talk.1', $content)) ?>

<script type="text/javascript">
(function($){

	$(function(){
		$('table[data-item="table"]')
			.on('change', ':checkbox, :radio', function(){
				var _this = $(this);

				if (_this.prop('checked'))
				{
					_this.parents('tr:first').find('> td').addClass('tdToggleBg');
				}
				else
				{
					_this.parents('tr:first').find('> td').removeClass('tdToggleBg');
				}

			})
			.find('td[data-no]')
				.on('click', function(event){

					var _this = $(this);

					if (!$(event.target).is(':input'))
					{

						$(':checkbox[data-no="' + _this.attr('data-no') + '"]')
							.prop('checked', function(idx, old){
								return !old;
							})
							.trigger('change')
						;

					}

				})
			.end()
			.find(':checkbox')
				.trigger('change')
		;
	});

})(jQuery);
</script>
