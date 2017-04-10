<?

if (! function_exists('__imageBeforeSave')) {
  function __imageBeforeSave($value, $params, $data) {
    if ($value['error'] <= 0) {
      return fileCopy($value, $params->path);
    } else
      return false;
  }
}

?>

<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_length', array('value' => 64));?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <div class="input">
    <label>Директория</label>
    <?=$form->input('Params.path', array('type' => 'text'));?>
    <p>Путь к директории куда будут сохраняться изображения</p>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input image">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'file')); ?>
    <? if (isset($this->data[$field->alias]) && ! empty($this->data[$field->alias])): ?>
      <div class="preview-image">
        <img src="<?=$this->data[$field->alias];?>" style="max-width: 50%;" />
      </div>
    <? endif; ?>
  </div>
<? endif; ?>