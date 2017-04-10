<?=$form->create('Group');?>
  <?=$form->hidden('id');?>
  <?=$form->hidden('model_id', array('value' => $this->params['url']['model_id']));?>
  <fieldset>
    <h3>Группа полей</h3>
    <div class="reducer">
      <div class="input">
        <label>Имя</label>
        <?=$form->input('name');?>
      </div>
      <div class="input checkbox r">
        <?=$form->input('is_hidden', array('type' => 'checkbox'));?>
        <label>Скрыть группу</label>
      </div>
      <div class="input">
        <label>Шаблон вывода</label>
        <?=$form->input('file', array('type' => 'select', 'options' => $viewTemplates, 'keys' => true));?>
        <p>Файл шаблона должен быть в models/form/groups</p>
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
  </fieldset>
<?=$form->end();?>