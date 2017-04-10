<?=$form->create('Setting'); ?>
<fieldset>
	<h3>Основное</h3>
	<div class="reducer">
		<div class="input">
			<label>Домен</label>
			<?=$form->input('domain');?>
		</div>
		<div class="input">
			<label>Общий заголовок</label>
			<?=$form->input('site-title');?>
			<p>Отображается для всех страниц в пользовательской вкладке браузера</p>
		</div>
		<div class="input">
			<label>Почтовые уведомления</label>
			<?=$form->input('site-email');?>
			<p>Почтовые адреса через запятую</p>
		</div>
    <div class="input submit">
      <input type="submit" value="Сохранить" /> 
    </div>
	</div>
</fieldset>
<fieldset>
	<h3>Основное</h3>
	<div class="reducer">
		<div class="input">
			<label>Общая наценка</label>
			<?=$form->input('markup', array('style' => 'width: 59px'));?> %
		</div>
		<div class="input">
			<label>Группы пользователей</label>
			<?=$form->input('profileGroups', array('type' => 'textarea', 'style' => 'height: 100px; margin-bottom: 0'));?>
			<p>
				 Через запятую
			</p>
		</div>
    <div class="input submit">
      <input type="submit" value="Сохранить" /> 
    </div>
	</div>
</fieldset>
<?=$form->end();?>