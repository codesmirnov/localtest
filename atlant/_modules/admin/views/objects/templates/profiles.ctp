<?

	$model = new Model(array('table' => 's_profile_groups'));
	$groups = $model->find('index', array('fields' => array('alias', 'title')));

?>

<div class="controll-object-table" href="<?=$this->params['current']['url'];?>/">
	<div class="controlls">
		С отмеченными:
		<span id="delete" class="java" style="color: red"><b>&times;</b> Удалить</span>
	</div>
	<table class="decor">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
			<th style="width: 27px;"><?=$html->paramLink('ID', 'sort=id', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Имя', 'sort=firstname', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Email', 'sort=email', array('toggleValue' => true));?></th>
			<th><?=$html->paramLink('Компания', 'sort=company_name', array('toggleValue' => true));?></th>
			<th>Группа</th>
			<th>Скидка</th>
		</tr>
		<? foreach($items as $item): ?>
			<tr itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
				<td><?=$item->id;?></td>
				<td><?=$html->link($item->firstname, $item->id);?></td>
				<td><?=$html->link($item->email, $item->id);?></td>
				<td><?=$html->link($item->company_name, $item->id);?></td>
				<td><?=$groups[$item->group];?></td>
				<td><?=$item->discount;?> %</td>
			</tr>
		<? endforeach; ?>
	</table>
</div>