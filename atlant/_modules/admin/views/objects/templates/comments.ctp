<div class="controll-list">
	<div class="controlls" href="<?=$this->params['current']['url'];?>/">
		<i>С отмеченными:</i>
		<span class="java drop">Сбросить</span>,
		<span class="java public">Опубликовать</span>,
		<span class="java hide">Скрыть</span> <i>или</i> 
		<span class="java delete red">&times; Удалить</span>
	</div>
	
  <? if (! empty($items)): ?>
	<ul class="item-list item-list-controll item-list-advert select-ban">
	  <? foreach ($items as $item): ?>
	    <li itemid="<?=$item->id;?>" class="<?=! $item->is_public ? ' is_hidden' : '';?>">
	      <p>
          <a href="<?=$html->href($item->id);?>">
            <?=strip_tags(textlimiter($item->text, 100) . ' ...');?>
          </a>
        </p>
				<i><?=$custome->date($item->created);?></i>
	    </li>
	  <? endforeach; ?>
	</ul>
  <? endif; ?>
</div>