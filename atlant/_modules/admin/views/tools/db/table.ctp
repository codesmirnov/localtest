<? if (! empty($this->data)): ?>

<div class="tools">
	<span class="acv">Структура таблицы</span>
	<?=$html->link('Обзор данных', 'data'); ?>
</div>

<?=$form->create('Table', array('enctype' => 'multipart/form-data')); ?>

<?=$form->tableInputs(
		'Fields',
		array(
			'name'    => 'Имя поля',
			'type'    => array('type' => 'select', 'options' => $mysqlTypes, 'th' => 'Тип данных'),
			'length'  => 'Длина',
			'null'    => array('type' => 'checkbox', 'th' => 'NULL')
		),
		array('class' => 'decor table-inputs')
	);
?>

<div class="form-line">
	<div class="submit reducer">
		<?=$form->submit('Сохранить');?>
	</div>
</div>

<?=$form->end();?>

<? endif; ?>