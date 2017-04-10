<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 32)); ?>
  <div class="input">
    <label>Параметры</label>
    <?=$form->input('Params.options', array('type' => 'textarea','style' => 'width: 619px'));?>
  </div>
  <div class="input">
    <label>По умолчанию</label>
    <?=$form->input('Params.default', array('type' => 'text'));?>
  </div>
  <div class="input checkbox r">
    <?=$form->input('Params.is_keyvalue', array('type' => 'checkbox', 'default' => 1));?>
    <label>Ключ равен значению</label>
  </div>
  <script type="text/javascript">
    $('#_params_is_keyvalue').change(function() {
      if ($(this).is(':checked')) {
        $('#_params_field_type').val('varchar');
        $('#_params_field_length').val('32');
      } else {
        $('#_params_field_type').val('int');
        $('#_params_field_length').val('11');
      }

    }).each(function() {
      $(this).change();
    })
  </script>
<? elseif (isset($render) && $render): ?>
  <div class="input radio-group">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?
      $params->options = parseArrayString($params->options);   
      
      if (! empty($params->invitation)) {
        $params->options = array_merge(array('-1' => $params->invitation), $params->options);
        $params->default = -1;
      }
    ?>
    <?=$form->input($field->alias, array('type' => 'radio', 'options' => $params->options, 'key' => $params->keyvalue, 'default' => $params->default)); ?>
  </div>
<? endif; ?>