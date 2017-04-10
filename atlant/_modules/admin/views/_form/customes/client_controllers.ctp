<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 32));?>
<? elseif (isset($render) && $render): ?>
<div class="input">
  <label>Файл контроллера</label>
  <? $controllers = array_diff(scandir(MODS . 'client' . DS . 'controllers'), array('.', '..')); ?>
  <?=$form->input('controller', array('type' => 'select', 'options' => array_merge(array('' => 'Наследовать'), keyValue($controllers)), 'href' => $this->params['current']['url'] . '/_setup/'));?>
</div>
<? endif; ?>

<script>
  $(function() {
    $('#_controller').change(function() {
      var file = $(this).val();
      if (file != '')
      $.get($(this).attr('href') + file, {'id' : $('#_id').val()}, function(data) {
        $('#controller-setup').remove();
        $('form').append('<div id="controller-setup">');
        $('#controller-setup').append(data);
      })
    }).each(function() {
      $(this).change();
    })    
  })
</script>