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
			<th><?=$html->paramLink('Наценка', 'sort=markup', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Срок поставки', 'sort=delivery', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Поставщик прайса', 'sort=profile_id', array('toggleValue' => true));?></th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=! $item->is_checked ? ' class="red"' : '';?><?=! $item->is_public ? ' class="is_hidden"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$html->link($item->{$titleField}, $item->id, array('rel' => $item->id));?></td>
				<td><?=$item->markup;?> %</td>
				<td><?=$item->delivery;?> дн.</td>
				<td><?=$item->profile_id == 0 ? '-' : $item->Profile->email;?></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>