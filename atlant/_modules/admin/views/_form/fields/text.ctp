<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type',   array('value' => 'varchar'));?>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('Params.class', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Стили CSS</label>
    <?=$form->input('Params.style', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Примечание справа</label>
    <?=$form->input('Params.right_hint', array('type' => 'text', 'default' => '', 'style' => 'width: 32px'));?>
  </div>
  <div class="input checkbox r">
    <?=$form->input('Params.is_numeric', array('type' => 'checkbox'));?>
    <label>Только цифры</label>
  </div>
  <div class="input">
    <label>Максимальная длина</label>
    <?=$form->input('Params.field_length', array('type' => 'text', 'default' => 32, 'style' => 'width: 28px'));?>
  </div>
  <div class="input">
    <label>По умолчанию</label>
    <?=$form->input('Params.default', array('type' => 'text'));?>
  </div>
  <div class="input checkbox r b">
    <?=$form->input('is_site_search', array('type' => 'checkbox'));?>
    <label for="_is_site_search">Использовать</label> в поиске по сайту
  </div> 
  <div class="input checkbox r">
    <?=$form->input('is_past_data_site', array('type' => 'checkbox'));?>
    <label for="_is_past_data_site">Добавить</label> текущие записи в поиске по сайту
  </div>
  <div class="input checkbox r b">
    <?=$form->input('is_atlant_search', array('type' => 'checkbox'));?>
    <label for="_is_atlant_search">Использовать</label> в поиск по атланту
  </div> 
  <div class="input checkbox r">
    <?=$form->input('is_past_data_atlant', array('type' => 'checkbox'));?>
    <label for="_is_past_data_atlant">Добавить</label> текущие записи в поиск по атланту
  </div>
  <script type="text/javascript">
    $('#_params_is_numeric').change(function() {
      if (! $(this).is(':checked')) {
        $('#_params_field_type').val('varchar');
      } else {
        $('#_params_field_type').val('int');
        $('#_params_field_length').val('11');
      }

    }).each(function() {
      $(this).change();
    })
  </script>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?=$form->input($field->alias, array('type' => 'text', 'class' => $params->class, 'style' => $params->style, 'default' => $params->default)); ?> <?=$params->right_hint;?>
  </div>
<? endif; ?>