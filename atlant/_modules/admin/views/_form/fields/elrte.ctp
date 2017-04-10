<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'text'));?>
  <div class="input checkbox r">
    <?=$form->input('required', array('type' => 'checkbox'));?>
    <label>Обязательное к заполнению</label>
  </div>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('class', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Стили CSS</label>
    <?=$form->input('style', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Значение по умолчанию</label>
    <?=$form->input('default', array('type' => 'text'));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <? if (! $this->ajax): ?>
    <?=$this->element('/_form/fields/elrte-resources'); ?>
  <? endif; ?>
  <?=$form->input($field->alias, array('type' => 'textarea', 'class' => 'elrte')); ?>
<? endif; ?>