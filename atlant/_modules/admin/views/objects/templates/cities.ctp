<div class="filter">
	<i>Выводить:</i>
	<? if (! isset($this->params['url']['order'])): ?>
		<span>По алфавиту</span>
	<? else: ?>
		<?=$html->paramLink('По алфавиту', 'order=title');?>
	<? endif; ?>
  <?=$html->paramLink('По регионам', 'order=region');?>
  <?=$html->paramLink('По позициям', 'order=pos');?>
</div>

<? if (! empty($items)): ?>
	<? if (! isset($this->params['url']['order']) || $this->params['url']['order'] == 'title'): ?>
	<? $letter = mb_substr($items[0]->title, 0, 2); ?>
		<div class="character clear">
		  <div class="row">
		    <h1><?=$letter;?></h1>
		    <ul class="item-list">
		      <? foreach ($items as $i => $item): ?>
		        <? 
		        	$char = trim(mb_substr($item->title, 0, 2));
		          if ($letter != $char && ! is_numeric($char)) {
		            $letter = $char; 
		            echo '</ul></div><div class="row"><h1>' . $letter . '</h1><ul class="item-list">'; 
		          } 
		        ?>
		        <li class="col"><?=$html->link($item->title, $item->id);?></li>
		      <? endforeach; ?>
		    </ul>
		  </div>
		</div>
	<? elseif ($this->params['url']['order'] == 'region'): ?>
		<? $region = $items[0]->region; ?>
		<div class="subject-heading clear">
		  <div class="row">
		    <h2><?=$region;?></h2>
		    <ul class="item-list">
		      <? foreach ($items as $i => $item): ?>
		        <? 
		          if ($region != $item->region) {
		          	$region = $item->region;
		            echo '</ul></div><div class="row"><h2>' . $region . '</h2><ul class="item-list">'; 
		          } 
		        ?>
		        <li class="col"><?=$html->link($item->title, $item->id);?></li>
		      <? endforeach; ?>
		    </ul>
		  </div>
		</div>
	<? elseif ($this->params['url']['order'] == 'pos'): ?>
		<p class="hint">Показаны только самые популярные:</p>
    <ul class="item-list sortable" action="<?=$html->href('order');?>">
    	<? foreach ($items as $i => $item): ?>
	    	<? if ($item->pos <= 1): ?>
		    	<li class="col sortitem" rel="<?=$item->id;?>"><?=$html->link($item->title, $item->id);?></li>
	      <? endif; ?>
			<? endforeach; ?>
    </ul>
	<? endif; ?>
<? endif; ?>