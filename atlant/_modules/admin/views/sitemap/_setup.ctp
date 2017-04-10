<fieldset>
  <h3>Настройки</h3>
  <div class="reducer">
    <div class="input">
      <label>Модель</label>
      <?=$form->input('Params.model', array('type' => 'select', 'options' => $models));?>
    </div>
    <div class="input submit">
      <input type="submit" value="Сохранить" /> 
      &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
    </div>
  </div>
</fieldset>