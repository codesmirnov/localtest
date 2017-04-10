<? if (! empty($items)): ?>
	<? $letter = mb_substr($items[0]->name, 0, 2); ?>
	<div class="character clear">
	  <div class="row">
	    <h1><?=$letter;?></h1>
	    <ul class="item-list">      
	      <? foreach ($items as $i => $item): ?>
	        <? 
	          if ($letter != mb_substr(iconv('UTF-8','WINDOWS-1251',$item->name), 0, 1)) {
	            $letter = mb_substr(iconv('UTF-8','WINDOWS-1251',$item->name), 0, 1); 
	            echo '</ul></div><div class="row"><h1>' . $letter . '</h1><ul class="item-list">'; 
	          } 
	        ?>
	        <li class="col">
	        	<a href="<?=$html->href($item->id);?>">
              <? if ($item->_check('Photos')): ?>
              <img src="<?=$image->crop($item->Photos[0]->path, 20, 20);?>" />
              <? endif; ?>
							<?=$item->name;?>
						</a>
					</li>
	      <? endforeach; ?>
	    </ul>
	  </div>
	</div>
<? endif; ?>