<?

if (! function_exists('__fileBeforeSave')) {
  function __fileBeforeSave($value, $params, $data) {
    if ($value['error'] <= 0) {
      $ex = explode(',',$params->extensions);
      return fileCopy($value, $params->path, array('extensions' => $ex));
    } else
      return false;
  }
}

?>

<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_length', array('value' => 64));?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <div class="input">
    <label>Расширения</label>
    <?=$form->input('Params.extensions', array('type' => 'text'));?>
  </div>
  <div class="input">
    <label>Директория</label>
    <?=$form->input('Params.path', array('type' => 'text'));?>
    <p>Путь к директории куда будут сохраняться изображения</p>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input file">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'file')); ?>
    <?=$form->input('__' . $field->alias, array('type' => 'hidden', 'value' => $field->value)); ?>
  </div>
<? endif; ?>