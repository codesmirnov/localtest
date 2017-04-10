<ul class="tree models-tree sortable" href="<?=$this->param['current']['url'];?>">
  <? foreach ($models as $item): ?>
    <li itemid="<?=$item->id;?>" class="sortitem">
      <?=$html->link($item->name, $item->id); ?>
    </li>
  <? endforeach; ?>
</ul>