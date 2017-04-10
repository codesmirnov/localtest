<?

if (! function_exists('__aliasBeforeSave')) {
  function __aliasBeforeSave($value, $params, $data) {
    if (empty($value) && isset($data['title'])) {
      $value = translit_utf8(textlimiter($data['title'], 50));
    }
    
    return $value;
  }
}

?>

<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => '100'));?>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'text')); ?>
    <? if (isset($_data->url)): ?>
      <p>
        <?=$_data->url;?>
      </p>
    <? endif; ?>
  </div>
<? endif; ?>