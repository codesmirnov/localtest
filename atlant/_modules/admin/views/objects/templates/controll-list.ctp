<div class="controll-list">
	<div class="controlls" href="<?=$this->params['current']['url'];?>/">
		<i>С отмеченными:</i>
		<span class="java drop">Сбросить</span>,
		<span class="java public"><b></b>Опубликовать</span>,
		<span class="java hide"><b></b>Скрыть</span> <i>или</i> 
		<span class="java delete red">&times; Удалить</span>
	</div>
  <? if (! empty($items)): ?>
	<ul class="item-list item-list-controll item-list-advert select-ban">
	  <? foreach ($items as $item): ?>
	    <li itemid="<?=$item->id;?>" class="<?=! $item->is_public ? ' is_hidden' : '';?>">
        <a href="<?=$html->href($item->id);?>"<?=isset($item->is_checked) && ! $item->is_checked ? 'class=" red"' : '';?>>          
          <? if ($item->_check('Photos')): ?>
          <img src="<?=$image->crop($item->Photos[0]->path, 30, 30);?>" />
          <? endif; ?>
          <?=$item->title;?>
        </a>
        <? if ($item->_check('notice')): ?>
	      <p><?=strip_tags(textlimiter($item->notice, 100) . ' ...');?></p>
        <? elseif ($item->_check('text')): ?>
	      <p><?=strip_tags(textlimiter($item->text, 100) . ' ...');?></p>
        <? endif; ?>
        <? if ($item->_check('created') && $item->created != '0000-00-00 00:00:00'): ?>
				<i><?=$custome->date($item->created);?></i>
        <? endif; ?>
	    </li>
	  <? endforeach; ?>
	</ul>
  <? endif; ?>
</div>