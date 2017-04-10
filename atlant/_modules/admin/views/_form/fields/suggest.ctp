<? if (isset($_setup) && $_setup): ?>
  <div class="input">
    <label>Таблица</label>
    <?=$form->hidden('Params.join', array('value' => 'belongsTo'));?>
    <?=$form->input('Params.table', array('type' => 'text', 'default' => '', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Ключ</label>
    <?=$form->input('Params.join_key', array('type' => 'text', 'default' => '', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Поля</label>
    <?=$form->input('Params.fields', array('type' => 'text', 'default' => 'id, title', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Ссыла на объект</label>
    <?=$form->input('Params.link', array('type' => 'text', 'default' => ''));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <div class="input">
    <label for="_<?=$field->alias;?>"><?=$field->name;?></label>
    <?
      $fields = explode(',', str_replace(' ', '', $params->fields));
      if (! empty($params->table)) {
      }
    ?>
    <div class="dropdown suggest suggest-field selected" table="<?=$params->table;?>" fields="<?=$params->fields;?>">
    	<div class="get">
    		<?=$form->input($field->alias, array('autocomplete' => 'off', 'value' => $this->data[ucfirst($field->alias)][$fields[1]]));?>
    		<?=$form->hidden($params->join_key, array('default' => 0));?>
    		<?= ! isset($decor) ? '<i>▼</i>' : $decor; ?>
    	</div>
    	<div class="list wrapper">
    		<ul class="ajax"></ul>
    		<ul class="default"></ul>
    	</div>
    </div>
    <? if ($params->link != '' && $this->data[ucfirst($field->alias)][$fields[1]]): ?>
      <a href="<?=$params->link;?><?=$this->data[ucfirst($field->alias)][$fields[0]];?>">
      <?=$this->data[ucfirst($field->alias)][$fields[1]];?>
      
      </a>
    <? endif; ?>
  </div>
<? endif; ?>