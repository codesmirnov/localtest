<? if ($this->params['current']['Params']['category']['show']): ?>  
  <div class="input url left"  style="clear: left;">    
    <?
      $category = $this->params['current']['Params']['category'];
      $value    = $this->params['url'][$category['key']];
    
      $model = new Model(array(
        'table' => $category['table']
      ));
      
      if ($value > 0) {
        $crumbs = $model->parents($value, array());
      } else 
        $crumbs[] = $model->find('first');
        
      foreach ($crumbs as &$crumb)
        $crumb['children'] = $model->children($crumb['id'], array('order' => 'pos'));
      
    ?>
    
    <? $html->start('li'); ?>
      <li>
        <%=$html->paramLink($item['title'], $key . '=' . $item['id']);%>      
        <% if ($item['children']): %>
          <%=$html->template('ul', array('children' => $item['children'], 'key' => $key));%>
        <% endif; %>
      </li>
    <? $html->end(); ?>
    
    <? $html->start('ul'); ?>
      <ul<%=isset($class)?' class="' . $class . '"':'';%>>
        <% foreach ($children as $item): %>
          <%=$html->template('li', array('item' => $item, 'key' => $key));%>
        <% endforeach; %>
      </ul>
    <? $html->end(); ?>
    
    <div class="crumbs">
      <? foreach ($crumbs as $i => $item): ?>
        <? if ($i > 0) : ?><span class="seporator"> &rarr; </span><? endif; ?>
        <? if (! empty($item['children'])): ?>
        <div class="java crumb">
          <?=$item['title'];?>
          <div class="selector">
            <?=$html->paramLink($item['title'], 'structure_id=' . ($item['id'] == 1 ? 0 : $item['id']), array('class' => 'crumb'));?>
            <?=$html->template('ul', array('children' => $item['children'], 'key' => $category['key']));?>
          </div>
        </div>
        <? else: ?>
        <div class="crumb"><?=$item['title'];?></div>
        <? endif; ?>
      <? endforeach; ?>
    </div>
  </div>
<? endif; ?>