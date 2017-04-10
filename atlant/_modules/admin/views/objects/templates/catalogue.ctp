<? $html->start('li'); ?>
  <li>
    <%=$html->link($item->name, $item->id, array('rel' => $item->id)); %>
    <% if ($item->children): %>
      <%=$html->template('ul', array('items' => $item->children));%>
    <% endif; %>
  </li>
<? $html->end(); ?>

<? $html->start('ul'); ?>
  <ul>
    <% foreach ($items as $item): %>
      <%=$html->template('li', array('item' => $item));%>
    <% endforeach; %>
  </ul>
<? $html->end(); ?>

<div class="tree tree-controll">
  <?=$html->template('ul', array('items' => $items)); ?>
</div>