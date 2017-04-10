<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <a href="<?=$this->data[$field->alias];?>" target="_blank"><?=$this->data[$field->alias];?></a>
  </div>
<? endif; ?>