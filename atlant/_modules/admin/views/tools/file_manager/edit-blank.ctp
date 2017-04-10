<form method="post">
	<fieldset>
		<h3><?=$filename;?></h3>
		<div class="reducer">
			<div class="input submit">
				<?=$form->submit('Сохранить', array('div' => false));?>
				<? if ($fileperm < 777): ?>
					<p class="error">
						Сохраниния файла невозможно. Измените права доступа. <?=$fileperm;?>
					</p>
				<? endif; ?>
			</div>
		</div>
	</fieldset>
	
	<textarea name="data[content]" style="width: 99.7%; height: 400px"><?=$file_content;?></textarea>
</form>