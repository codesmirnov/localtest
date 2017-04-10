<? if (isset($_setup) && $_setup): ?>
  <div class="input">
    <label>Классы CSS</label>
    <?=$form->input('Params.class', array('type' => 'text', 'default' => 'decor table-inputs'));?>
  </div>
  <div class="input">
    <label>Стили CSS</label>
    <?=$form->input('Params.style', array('type' => 'text', 'default' => ''));?>
  </div>
  <div class="input">
    <label>Модель</label>
    <? 
      $models = array();
      $r = $Model->find('list', array('fields' => array('id', 'name'), 'order' => 'pos')); 
      foreach ($r as $item)
        $models[$item['id']] = $item['name'];
    ?>
    <?=$form->input('Params.model', array('type' => 'select', 'options' => $models));?>
    <?=$form->hidden('Params.join', array('value' => 'hasMany')); ?>
  </div>
  <div class="input">
    <label>Ключ</label>
    <?=$form->input('Params.join_key'); ?>
  </div>    
  <div class="input">
    <label>Поля</label>
    <?=$form->input('Params.fields'); ?>
  </div>      
<? elseif (isset($render) && $render): ?>
  
  <?
    $alias = $field->alias;
    if (isset($_data->{$alias}))
      $data = $_data->{$alias};
      
    $Field = new Model(array('table' => '_sys_model_fields'));
    $fields = $Field->find('all', array(
      'conditions' => array('model_id' => $params->model)));
      
    $options = array();
    foreach ($fields as $field) {
      $option = array('th' => $field['name']);
      $p      = json_decode($field['params'], true);
      foreach (array('class','style','type') as $param)
        if (isset($p[$param]))
          $option[$param] = $p[$param];
          
      $options[$field['alias']] = $option;
    } 
  ?>
  
      
  <?=$form->tableInputs(
		$alias, $options, array('class' => $params->class, 'style' => $params->style, 'wrap' => 'div')
	); ?>

<? endif; ?>