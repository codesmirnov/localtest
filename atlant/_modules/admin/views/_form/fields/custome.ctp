<? if (isset($_setup) && $_setup): ?>
  <div class="input">
    <label>Шаблона</label>
    <? $templates = array_diff(scandir($this->root . 'views' . DS . '_form' . DS . 'customes'), array('.', '..')); ?>
    <?=$form->input('Params.template', array('type' => 'select', 'options' => $templates, 'keys' => true, 'href' => $ROOT . '/_setup/customes/'));?>
  </div>
  <script>
    $(function() {
      $('#_params_template').change(function() {
        $('#fieldset-field-setup').show();  
        $.get($(this).attr('href') + $(this).val(), {'id' : $('#_field_id').val()}, function(data) {
          $('#field-setup').append(data);
        })
      }).each(function() {
        $(this).change();
      })    
    })
  </script>
<? elseif (isset($render) && $render): ?>
  <?=$this->render(str_replace('.ctp', '', $params->template), '', array('render' => true, 'field' => $field, 'params' => $params), '_form' . DS . 'customes'); ?>
<? endif; ?>