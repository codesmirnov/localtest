<?

if (! function_exists('__dateBeforeSave')) {
  function __dateBeforeSave($value) {
    return $value['y'] . '-' . $value['m'] . '-' . $value['d'];
  }
}

?>

<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'date'));?>
  <div class="input">
    <label>Формат вывода</label>
    <?=$form->input('Params.format', array('default' => 'DMY'));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <? $params = json_decode($field->params); ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'date', 'maxYear' => date('Y') + 4, 'dateFormat' => $params->format)); ?>
  </div>
<? endif; ?>