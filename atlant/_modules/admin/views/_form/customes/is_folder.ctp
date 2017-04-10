<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'tinyint'));?>
  <?=$form->hidden('Params.field_length', array('value' => 1)); ?>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('Params.class', array('type' => 'text', 'default' => 'checkbox r'));?>
  </div>
  <div class="input checkbox r">
    <?=$form->input('Params.default', array('type' => 'checkbox'));?>
    <label>По умолчанию включен</label>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input<?=!empty($params->class)? ' '.$params->class:'';?>">
    <?=$form->input($field->alias, array('type' => 'checkbox', 'default' => $params->default)); ?>
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
  </div>
  <script>
    $(function() {
      $('#_<?=$field->alias;?>').change(function() {
        if ($(this).is(':checked')) {
          $('fieldset').hide();
          $(this).parents('fieldset').show();
        } else {
          $('fieldset').show();
        }
      }).each(function() {
        $(this).change();
      })
    })
  </script>
<? endif; ?>