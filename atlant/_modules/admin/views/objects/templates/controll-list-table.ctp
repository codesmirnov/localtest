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
			<th><?=$html->paramLink('Название', 'sort=' . $titleField, array('toggleValue' => true));?></th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=! $item->is_public ? ' class="is_hidden"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$html->link($item->{$titleField}, $item->id, array('rel' => $item->id));?></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>