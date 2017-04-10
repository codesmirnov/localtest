<div class="controll-object-table" href="<?=$this->params['current']['url'];?>/">
	<div class="controlls">
		С отмеченными:
		<span id="show" class="java">Опубликовать</span>,
		<span id="hide" class="java">Скрыть</span>
		<span id="delete" class="java" style="color: red"><b>&times;</b> Удалить</span>
	</div>
  <? $titleField = $this->params['current']['Params']['title_field']; ?>
	<table class="decor table-dnd">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
			<th><?=$html->paramLink('ID', 'sort=id', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Название', 'sort=title', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Артикул', 'sort=sku', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Цена', 'sort=price', array('toggleValue' => true));?></th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=! $item->is_public ? ' class="is_hidden"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$html->link($item->title, $item->id, array('rel' => $item->id));?></td>
				<td><?=$html->link($item->sku, $item->id, array('rel' => $item->id));?></td>
				<td><?=$custome->rur($item->price);?> руб.</td>
			</tr>
		<? endforeach; ?>
	</table>
</div>