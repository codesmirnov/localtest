<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 64));?>
  <?=$form->input('Params.support_fields', array('type' => 'textarea', 'style' => 'display: none', 'value' => json_encode(array(
    'lat' => array('float', 11),
    'lon' => array('float', 11) 
  ))));?>
  <div class="input">
    <label>По умолчанию</label>
    <?=$form->input('Params.default', array('type' => 'text'));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->hidden('lat'); ?>
    <?=$form->hidden('lon'); ?>
    <?=$form->input($field->alias, array('type' => 'text', 'class' => 'address', 'default' => $params->default)); ?>
  </div>
<? endif; ?>