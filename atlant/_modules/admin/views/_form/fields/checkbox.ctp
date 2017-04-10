<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'tinyint'));?>
  <?=$form->hidden('Params.field_length', array('value' => 1)); ?>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('Params.class', array('type' => 'text', 'default' => 'checkbox r'));?>
  </div>
  <div class="input checkbox r b">
    <?=$form->input('Params.default', array('type' => 'checkbox'));?>
    <label>По умолчанию включен</label>
  </div> 
  <div class="input checkbox r">
    <?=$form->input('is_search_condition', array('type' => 'checkbox'));?>
    <label for="_is_search_condition">Использовать</label> как условие добавление в поиск по сайту
  </div>   
<? elseif (isset($render) && $render): ?>
  <div class="input<?=!empty($params->class)? ' '.$params->class:'';?>">
    <?=$form->input($field->alias, array('type' => 'checkbox', 'default' => $params->default)); ?>
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
  </div>
<? endif; ?>