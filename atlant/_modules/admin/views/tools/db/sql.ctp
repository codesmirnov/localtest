<form id="db-sql-query" method="post">
	<fieldset>
		<h3>Запрос к БД</h3>
		<div class="reducer">
			<div class="input">
				<label>SQL запрос</label>
				<textarea name="query"><?=@$this->params['url']['query'];?></textarea>
			</div>
			<div class="input submit">
				<input type="submit" value="Отправить" />
			</div>
		</div>
	</fieldset>
</form>

<? if (! empty($this->data)): ?>
	<?=$this->render('table_data', 'clear');?>
<? endif; ?>