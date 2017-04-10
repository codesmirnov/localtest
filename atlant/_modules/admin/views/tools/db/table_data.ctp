<div class="tools">
	<?=$html->link('Структура таблицы', ':back'); ?>
	<span class="acv">Обзор данных</span>
</div>

<? if (! empty($this->data)): ?>
<div id="table-data">
	<table class="decor">
		<tr>
			<th class="controll"><input type="checkbox" /></th>
			<? foreach ($this->data[0] as $key => $row): ?>
				<? $before = ''; if (str_replace(' DESC', '', $this->params['url']['order']) == $key) $before = ($this->params['url']['order'] == $key ? '&darr;' : '&uarr;'); ?>
				<th><?=$html->paramLink($before . $key, 'order=' . $key . ($this->params['url']['order'] == $key ? '-' : ''));?></th>
			<? endforeach; ?>
		</tr>
		<? foreach ($this->data as $row): ?>
			<tr>
				<td class="controll"><input type="checkbox" /></td>
				<? foreach ($row as $key => $field): ?>
					<? if ($key == 'id'): ?>
					<td>
						<?=$html->link($field, $key . '/' . $field); ?>
					</td>
					<? else: ?>
					<td>
						<?
							$limit = 100;
							$field = htmlspecialchars($field);
							if (strlen($field) > $limit)
								$field = substr($field, 0, $limit) . ' ' . $html->link('...', $key);
							echo $field;
						?>
					</td>
					<? endif; ?>
				<? endforeach; ?>
			</tr>
		<? endforeach; ?>
	</table>
</div>
<? endif; ?>

<script language="javascript">
	function bodyResize() {
		var width = $('#table-data table').width() + 40;
		if ($('body').width() < width) {
			$('body').width(width);
			$('body .bk').width(width);
		}
	}
	window.onload = bodyResize();
</script>