<fieldset>
  <h3><?=$group->name;?></h3>
  <div class="reducer">
    <?=$fields;?>
    <div class="input submit">
      <input type="submit" value="Сохранить" /> 
      &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
    </div>
  </div>
</fieldset>