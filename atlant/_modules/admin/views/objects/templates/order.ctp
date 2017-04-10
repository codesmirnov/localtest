<div class="controll-object-table" href="<?=$this->params['current']['url'];?>/">
	<div class="controlls">
		С отмеченными:
		<span id="delete" class="java" style="color: red"><b>&times;</b> Удалить</span>
	</div>

	<?        
	  $statuses = Configure::read('statuses');      
	?>

	<table class="decor table-dnd">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
			<th><?=$html->paramLink('ID', 'sort=id', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Статус заказа', 'sort=status', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Дата поступления', 'sort=date', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Номер заказа', 'sort=number', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Имя покупателя', 'sort=name', array('toggleValue' => true));?></th>
      <th>Сумма заказа</th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=! $item->is_checked ? ' class="red"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$statuses[$item->status];?></td>
				<td><?=$item->created;?></td>
				<td><?=$html->link($item->number, $item->id, array('rel' => $item->id));?></td>
				<td><?=$item->firstname . ' ' . $item->lastname;?></td>
        <td><?=$custome->rur($item->price);?> руб.</td>
			</tr>
		<? endforeach; ?>
	</table>
</div>