<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 32)); ?>
  <div class="input">
    <label>Приглашение</label>
    <?=$form->input('Params.invitation', array('type' => 'text'));?>
  </div>
  <div class="input">
    <label>Параметры</label>
    <?=$form->input('Params.options', array('type' => 'textarea','style' => 'width: 619px'));?>
  </div>
  <div class="input">
    <label>Таблица</label>
    <?=$form->input('Params.table', array('type' => 'text', 'default' => '', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Поля</label>
    <?=$form->input('Params.fields', array('type' => 'text', 'default' => 'id, title', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Условие выбоки</label>
    <?=$form->input('Params.conditions', array('type' => 'textarea', 'default' => ''));?>
  </div>
  <div class="input checkbox r">
    <?=$form->input('Params.is_keyvalue', array('type' => 'checkbox'));?>
    <label>Ключ равен значению</label>
  </div>
  <script type="text/javascript">
    $('#_params_is_keyvalue').change(function() {
      if ($(this).is(':checked')) {
        $('#_params_field_type').val('varchar');
        $('#_params_field_length').val('32');
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
    <?
      if (! empty($params->table)) {
        $model = new Model(array(
          'table' => $params->table
        ));
        
        list($key, $value) = explode(',', str_replace(' ','',$params->fields));
        
        $options = $model->find('list', array(
          'conditions' => $params->conditions,
          'fields'     => array($key, $value)
        ));
        
        foreach ($options as &$option) {
          $option = $option[$value];
        }
        
        $params->options = $options;
      } else {
        $params->options = parseArrayString($params->options);  
      }
      
      if (! empty($params->invitation)) {
        $params->options = array_merge(array('-1' => $params->invitation), $params->options);
        $params->default = -1;
      }
      
    ?>
    <?=$form->input($field->alias, array('type' => 'select', 'options' => $params->options, 'keys' => $params->is_keyvalue, 'default' => $params->default)); ?>
  </div>
<? endif; ?>