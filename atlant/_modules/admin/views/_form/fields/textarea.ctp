<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 256)); ?>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('Params.class', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Стили CSS</label>
    <?=$form->input('Params.style', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>По умолчанию</label>
    <?=$form->input('Params.default', array('type' => 'text'));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'textarea', 'class' => $params->class, 'style' => $params->style, 'default' => $params->default)); ?>
  </div>
<? endif; ?>