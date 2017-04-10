<div class="controll-object-table" href="<?=$this->params['current']['url'];?>/">
	<div class="controlls">
		С отмеченными:
		<span id="show" class="java">Опубликовать</span>,
		<span id="hide" class="java">Скрыть</span>
		<span id="delete" class="java" style="color: red"><b>&times;</b> Удалить</span>
	</div>
	<table class="decor table-dnd">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
			<th><?=$html->paramLink('ID', 'sort=id', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Дата', 'sort=created', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Покупатель', 'sort=email', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Сумма', 'sort=pay_amount', array('toggleValue' => true));?></th>
		</tr>
		<? foreach($items as $item): ?>
			<tr>
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$custome->date($item->created);?></td>
				<td><?=$html->link($item->Profile->email, $item->id, array('rel' => $item->id));?></td>
				<td><?=$custome->rur($item->pay_amount);?> руб.</td>
			</tr>
		<? endforeach; ?>
	</table>
</div>