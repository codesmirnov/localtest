<?=$form->create('Field');?>
  <?=$form->hidden('id', array('id' => '_field_id'));?>
  <?=$form->hidden('model_id', array('default' => @$modelId));?>
  <?=$form->hidden('group_id', array('value' => $this->params['url']['group_id']));?>
  <fieldset>
    <h3>Поле</h3>
    <div class="reducer">
      <div class="input">
        <label>Имя</label>
        <?=$form->input('name');?>
      </div>
      <div class="input">
        <label>Псевдоним</label>
        <?=$form->input('alias');?>
        <?=$form->hidden('oldAlias', array('value' => $this->data['alias']));?>
      </div>
      <div class="input">
        <label>Шаблон вывода</label>
        <?=$form->input('file', array('type' => 'select', 'options' => $viewTemplates, 'keys' => true, 'href' => $ROOT . '/_setup/fields/'));?>
        <p>Файл шаблона должен быть в models/form/fields</p>
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
  </fieldset>
  <fieldset id="fieldset-field-setup" style="display: none;">
    <h3>Дополнительно</h3>
    <div class="reducer">
      <div id="field-setup" class="target">
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
  </fieldset>
<?=$form->end();?>

<script>
  $(function() {
    $('#_file').change(function() {
      $('#fieldset-field-setup').show();  
      $.get($(this).attr('href') + $(this).val(), {'id' : $('#_field_id').val()}, function(data) {
        $('#field-setup').html(data);
      })
    }).each(function() {
      $(this).change();
    })    
  })
</script>