<div class="filter">
  <i>Режим:</i>
  <? if (! isset($this->params['url']['mode']) && $this->params['url']['mode'] == 'sideform'): ?>
    <span>Форма по нажатию</span>
  <? else: ?>
    <?=$html->paramLink('Форма по нажатию', 'mode=sideform');?>
  <? endif; ?>
  <?=$html->paramLink('Галочками', 'mode=checkbox');?>
</div>
<? if ($this->params['url']['mode'] == 'sideform'): ?>

  <?=$this->element('/_form/fields/elrte-resources'); ?>
  
  <div id="site-map" class="conteinter _atlant-sideform" href="/admin/sitemap/">
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
      
      <?=$html->template('ul', array('items' => $map, 'class' => 'workspace tree tree-controll', 'id' => 'site-map-tree')); ?>
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
<? else: ?>
  <div class="filter">
    <i>Поле:</i>
    <? foreach ($fields as $field): ?>
      <?=$html->paramLink($field->name, 'field=' . $field->alias);?>
    <? endforeach; ?>
  </div>
      
  <? $html->start('li'); ?>
    <li itemid="<%=$item->id;%>">      
      <input type="checkbox"<%=$item->{$this->params['url']['field']} ? ' checked' : '';%> />
      <span class="java"><%=$item->title;%></span>
      <% if ($item->children): %>
        <%=$html->template('ul', array('items' => $item->children));%>
      <% endif; %>
    </li>
  <? $html->end(); ?>
  
  <? $html->start('ul'); ?>
    <ul<%=isset($class)?' class="' . $class . '"':'';%><%=isset($href)?' href="' . $href . '"':'';%><%=isset($field)?' field="' . $field . '"':'';%>>
      <% foreach ($items as $item): %>
        <%=$html->template('li', array('item' => $item));%>
      <% endforeach; %>
    </ul>
  <? $html->end(); ?>
  
  <?=$html->template('ul', array('items' => $map, 'class' => 'tree tree-checkbox select-ban', 'href' => $this->params['current']['url'] . '/', 'field' => $this->params['url']['field'])); ?>
    
  <script>
    $('.tree-checkbox').each(function() {
      var parent = this;
      
      $('input', this).click(function() {
        $.post($(parent).attr('href') + 'checkbox?field=' + $(parent).attr('field'), {'value' : $(this).attr('checked') ? 0 : 1, 'id' : $(this).parent().attr('itemid')}, function() {          
        })
      })
      
      $('.java', this).click(function() {
        $(this).prev().click();
      })
    })
  </script>
<? endif; ?>