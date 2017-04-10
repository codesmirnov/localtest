<div class="controll-object-table" href="<?=$this->params['current']['url'];?>/">
	<table class="decor table-dnd">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
			<th><?=$html->paramLink('ID', 'sort=id', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Название', 'sort=title', array('toggleValue' => true));?></th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=$item->is_common ? ' class="is_hidden"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$html->link($item->title, $item->id, array('rel' => $item->id));?></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>