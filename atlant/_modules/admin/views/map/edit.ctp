<?=$form->create('Map', array('id' => '_map_form'));?>
  <?=$form->hidden('id');?>
  <fieldset>
    <h3>Страница</h3>
    <div class="reducer">
      <div class="input">
        <label>Имя страницы</label>
        <?=$form->input('title');?>
      </div>
      <div class="input">
        <label>Псевдоним</label>
        <?=$form->input('alias');?>
        <p><?=$_data->url;?></p>
      </div>
      <div class="input">
        <label>Файл контроллера</label>
        <?=$form->input('controller', array('type' => 'select', 'options' => $controllers, 'keys' => true, 'href' => $this->params['current']['url'] . '/_setup/'));?>
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
  </fieldset>
  <div id="_setup-block">  
  </div>
<?=$form->end();?>

<script>
  $(function() {
    $('#_controller').change(function() {
      var file = $(this).val();
      if (file != '_map.php')
        $.get($(this).attr('href') + file, {'id' : $('#_id').val()}, function(data) {
          $('#_setup-block').html(data);
        })
    }).each(function() {
      $(this).change();
    })    
  })
</script>