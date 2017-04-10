<div id="site-map" class="conteinter _atlant-sideform" href="<?=$this->params['current']['url'];?>">
	<div class="spacer">
    
    <? $html->start('li'); ?>
      <li>
        <%=$html->link($item->title, $item->id, array('rel' => $item->id)); %>
        <% if ($item->children): %>
          <%=$html->template('ul', array('items' => $item->children));%>
        <% endif; %>
      </li>
    <? $html->end(); ?>
    
    <? $html->start('ul'); ?>
      <ul<%=isset($id)?' id="' . $id . '"':'';%><%=isset($class)?' class="' . $class . '"':'';%>>
        <% foreach ($items as $item): %>
          <%=$html->template('li', array('item' => $item));%>
        <% endforeach; %>
      </ul>
    <? $html->end(); ?>
    
    <?=$html->template('ul', array('items' => $items, 'class' => 'workspace tree tree-controll', 'id' => 'site-map-tree')); ?>
		<div id="site-map-branch-edit" class="sideform">
			<div class="tabs-nav handler wrap">
				<span class="l back">&larr; <span class="java">Карта сайта</span></span>
				<span class="m current">Страница</span>
			</div>
			<div class="target wrap bk">
			</div>
		</div>
		<div id="map-branch-edit-back">
		</div>
	</div>
</div>