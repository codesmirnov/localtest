<ul class="columns">
  <? foreach ($objects as $object): ?>
    <li>
      <h2>
        <?=$html->link($object->title, $object->url);?>
        <sub><?=$object->count;?></sub>
      </h2>
      <? if (isset($object->Items) && ! empty($object->Items)): ?>
          <ul>
          <? foreach ($object->Items as $item): ?>
            <? if ($item->_check('is_checked')): ?>
            <li<?=$item->is_checked == 0 ? ' class="red"' : '';?>>
            <? else: ?>
            <li>
            <? endif; ?>
              <?=$html->link($item->{$object->Params->title_field}, $object->url . '/'. $item->id); ?>
            </li>
          <? endforeach; ?>
          <li><?=$html->link('...', $object->url);?></li>
        </ul>
      <? endif; ?>
    </li>
  <? endforeach; ?>
</ul>