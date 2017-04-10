<div class="controll-list">
	<div class="controlls" href="<?=$this->params['current']['url'];?>/">
		<i>С отмеченными:</i>
		<span class="java drop">Сбросить</span>,
		<span class="java public"><b></b>Опубликовать</span>,
		<span class="java hide"><b></b>Скрыть</span> <i>или</i> 
		<span class="java delete red">&times; Удалить</span>
    <div id="discount-tool" class="discount-tool">
      <div class="input">
        <label>Скидка</label>
        <input type="text" value="0" style="width: 27px;" />%
        <span class="java">Назначить</span>
      </div>
    </div>
	</div>
  <? if (! empty($items)): ?>
	<ul class="product-list item-list item-list-controll item-list-advert select-ban">
	  <? foreach ($items as $item): ?>
	    <li itemid="<?=$item->id;?>" class="lc c-6-2 product-item <?=! $item->is_public ? ' is_hidden' : '';?>" price="<?=$item->price;?>">
        <a href="<?=$html->href($item->id);?>">          
          <? if ($item->_check('Photos')): ?>
          <img src="<?=$image->crop($item->Photos[0]->path, 30, 30);?>" />
          <? endif; ?>
          <?=$item->title;?>
        </a>
        <div class="sku">
          <?=$item->sku;?>
        </div>
        <div class="price">
          <?
    
          $price    = $item->price;
          $discount = $item->discount;
          $percent  = $discount/$price * 100;
          
          ?>
          <? if ($discount > 0): ?>
            <div class="old-price price"><?=$custome->rur($price);?></div> 
            <div class="price discount"><?=$custome->rur($discount);?> руб.<span class="percent"> = <?=round(100-$percent);?>%</span></div> 
          <? else: ?>
            <div class="price"><?=$custome->rur($price);?> руб.</div>
          <? endif; ?>
        </div>
	      <p><?=strip_tags(textlimiter($item->notice, 300) . ' ...');?></p>
	    </li>
	  <? endforeach; ?>
	</ul>
  <? endif; ?>
</div>

<script>
  $(function() {
    $('#discount-tool').each(function() {
      var parent = this;
      var target = $('.product-list');
      var href   = $('.controll-list .controlls').attr('href');
      
      $('input', this).keyup(function() {
        var val = parseInt($(this).val().replace(/[^\d]+/g, ''));
        if (val < 0 || ! val)
          val = 0;
        if (val > 100)
          val = 100;
        $(this).val(val);
      })
      
      $('.java', this).click(function() {
        var d     = $('input', parent).val();
        var items = new Array();
        
        if ($('.acv', target).size()) {
          $('.acv', target).each(function(i) {
            var price = $(this).attr('price');
            var id    = $(this).attr('itemid');
            var value = (d == 0) ? 0 : price - (price * d / 100);
            
            value = Math.round(value/10) * 10;
          
            items[i] = {field : 'discount', id : id, value : value};
            
            if (value == 0)
              $('.price', this).html('<div class="price">'+$.priceFormat(price) + ' руб.</div>');
            else
              $('.price', this).html('<div class="old-price price">'+$.priceFormat(price)+'</div> <div class="discount price">'+$.priceFormat(value)+' руб.</div><span class="percent"> = '+d+'%</span>');
          })
        }
            
        $.post(href + 'fields', {'items' : items}, function() {})          
      })
    })
  })
</script>