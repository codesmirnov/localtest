<?=$form->create('Table', array('enctype' => 'multipart/form-data')); ?>

<fieldset>
  <div class="reducer">
    <div class="input">
      <label>Название таблицы</label>
      <?=$form->input('name', array('class' => 'required title'));?>
    </div>
  </div>
</fieldset>

<?=
	$form->tableInputs(
		'Fields',
		array(
			'name' => 'Имя поля',
			'type' => array('type' => 'select', 'options' => $mysqlTypes, 'th' => 'Тип данных'),
			'length' => 'Длина',
			'null' => array('type' => 'checkbox', 'th' => 'NULL'),
			'auto' => array('type' => 'checkbox', 'th' => 'Автоинкремент'),
			'primary' => array('type' => 'checkbox', 'th' => 'PRIMARY')
		),
		array('class' => 'table-inputs decor')
	);
?>

<div class="form-line">
	<div class="submit reducer">
		<?=$form->submit('Добавить');?>
		<?=$form->submit('Очистить');?>
	</div>
</div>

<?=$form->end();?>
