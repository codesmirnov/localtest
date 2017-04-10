<?=$form->create('Table');?>
	<fieldset>
		<h3>Запись</h3>
		<div class="reducer">
			<? foreach ($this->data['Table'] as $key => $value): ?>
				<? if ($key == 'id'): ?>
				<div class="input">
					<label><?=$key;?></label>
					<?=$form->input($key, array('type' => 'text')); ?>
				</div>
				<? else: ?>
				<div class="input">
					<label><?=$key;?></label>
					<?=$form->input($key); ?>
				</div>
				<? endif; ?>
			<? endforeach; ?>
		</div>
	</fieldset>
<?=$form->end();?>