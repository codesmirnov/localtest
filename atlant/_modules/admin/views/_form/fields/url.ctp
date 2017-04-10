<?
  if (! function_exists('__urlBeforeSave')) {    
    function __urlBeforeSave($value, $params, $data) {
      if (isset($data[$params->join_key]) && isset($data['alias'])) {   
        $join  = $data[$params->join_key];
        $model = new Model(array(
          'table' => $params->table
        ));
        
        $url = $model->field('url', array('id' => $join)) . '/' . $data['alias'];
        return $url;
      }
    }      
  }
?>

<? if (isset($_setup) && $_setup): ?>
  <?=$form->hidden('Params.field_type', array('value' => 'varchar'));?>
  <?=$form->hidden('Params.field_length', array('value' => 255));?>
  <?=$form->hidden('Params.join', array('value' => 'belongsTo')); ?>
  <div class="input">
    <label>Таблица</label>
    <?=$form->input('Params.table');?>
  </div>
  <div class="input">
    <label>Ключ</label>
    <?=$form->input('Params.join_key');?>
  </div>
<? elseif (isset($render) && $render): ?>
    
  <? $html->start('li'); ?>
    <li>
      <span class="java crumb-<%=$item['id'];%>" itemid="<%=$item['id'];%>"><%=$item['title'];%></span>
      <% if ($item['children']): %>
        <%=$html->template('ul', array('items' => $item['children']));%>
      <% endif; %>
    </li>
  <? $html->end(); ?>
  
  <? $html->start('ul'); ?>
    <ul<%=isset($class)?' class="' . $class . '"':'';%>>
      <% foreach ($items as $item): %>
        <%=$html->template('li', array('item' => $item));%>
      <% endforeach; %>
    </ul>
  <? $html->end(); ?>
    
  <div class="input url">    
    <?
      $key   = $params->join_key;
      $value = $_data->{$key};
    
      $model = new Model(array(
        'table' => $params->table
      ));
      
      if ($value > 0) {
        $crumbs = $model->parents($value);
      } else 
        $crumbs[] = $model->find('first');
        
      foreach ($crumbs as &$crumb)
        $crumb['children'] = $model->children($crumb['id'], array('order' => 'pos'));
      
    ?>
    
    <label><?=$field->name;?></label>
    <div class="crumbs">
      <?=$form->hidden($field->alias); ?>
      <?=$form->hidden($params->join_key, array('default' => $crumbs[0]['id'])); ?>
      <? foreach ($crumbs as $i => $item): ?>
        <? if ($i > 0) : ?><span class="seporator"> &rarr; </span><? endif; ?>
        <? if (! empty($item['children'])): ?>
        <div class="java crumb">
          <?=$item['title'];?>
          <div class="selector">
            <span class="crumb"><?=$item['title'];?></span>
            <?=$html->template('ul', array('items' => $item['children']));?>
          </div>
        </div>
        <? else: ?>
        <div class="crumb"><?=$item['title'];?></div>
        <? endif; ?>
      <? endforeach; ?>
    </div>
  </div>
<? endif; ?>