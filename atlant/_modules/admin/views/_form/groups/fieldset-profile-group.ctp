<fieldset>
  <h3><?=$group->name;?></h3>
  <div class="reducer">
    <?=$fields;?>
    <div class="input submit">
      <input type="submit" value="Сохранить" /> 
      <? if (! $this->data['is_common']): ?>
      &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
	    <? endif; ?>
    </div>
  </div>
</fieldset>