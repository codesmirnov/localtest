<p>Для полного управления файлами и каталогами воспользуйтесь менеджером <a href="/admin/dev/files/el-finder/">El Finder</a></p>

<input id="relative-path" type="hidden" value="<?=$relative_path;?>" />

<? $image->cache = '_cache'; ?>

<table id="file-list" class="decor">
	<tr>
		<th class="controll"><input id="select-all" type="checkbox" /></th>
		<th class="preview"></th>
		<th></th>
		<th></th>
	</tr>
	<? if (! empty($this->params['pass'])): ?>
		<tr>
			<td></td>
			<td><?=$html->link('..', ':back');?></td>
		</tr>
	<? endif; ?>
	<?=$this->element('file-list-tr', array('files' => $files['dir'], 'type' => 'dir'));?>
  <? if (! empty($files['files-by-type'])): ?>
	<? foreach ($files['files-by-type'] as $type => $files): ?>
		<?=$this->element('file-list-tr', array('files' => $files, 'type' => $type));?>
	<? endforeach; ?>
  <? endif; ?>
</table>

<script language="javascript">
	$(function() {
		function selection(target) {
			var list = new Array();
			$('tr td.controll input:checked', target).each(function(i) {
				var parent = $(this).parents('tr');
				list[i] = $('a', parent).text();
			});
			return list;
		}

		$('#file-list').each(function() {
			var parent = this;

			$('#select-all').change(function() {
				if ($(this).attr('checked'))
					$('.controll input', parent).removeAttr('checked');
				else $('.controll input', parent).attr('checked', 'true');
			});

			$('#save').click(function() {
				var path = $('#relative-path').val();
				var files = selection(parent);
				$.post('/admin/dev/files/save', {'data[files]' : files, 'data[path]' : path}, function() {
					window.location = '/download.zip';
				});
			});

			$('#del').click(function() {
				var path = $('#relative-path').val();
				var files = selection(parent);
				$.post('/admin/dev/files/del', {'data[files]' : files, 'data[path]' : path}, function() {
					$('tr td.controll input:checked', parent).each(function(i) {
						$(this).parents('tr').remove();
					});
				});
			});

			$('tr', this).bind('click', function() {
				if (! $(this).hasClass('edit')) {
					$('td.name a', parent).show();
					$('td.name input', parent).hide();
					$('tr').removeClass('edit');
				}

				if (! $(this).is('a, input')) {
					var controll = $('.controll input', this);
					if (! controll.attr('checked'))
						controll.attr('checked', 'true');
					else controll.removeAttr('checked');
				}
			});

			$('tr', this).bind('dblclick', function() {
				$('tr').removeClass('edit');
				$('td.name a', parent).show();
				$('td.name input', parent).hide();

				$(this).addClass('edit');
				$('td.name a', this).hide();
				$('td.name input', this).show();
			});
		});
	});
</script>

