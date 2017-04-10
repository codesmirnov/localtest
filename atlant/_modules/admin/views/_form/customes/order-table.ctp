
<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>

  <? $products = json_decode($this->data['products']); ?>

  <? if (! empty($products)) : ?>
  <div class="lc c-6-4">
    <div class="product-row th">
      <div class="lc c-6-2">
        Название товара
      </div>
      <div class="lc c-6-1">
        Количество
      </div>
      <div class="lc c-6-1 stop">
        Цена
      </div>
    </div>
    <? foreach ($products as $item): ?>
    <div class="product-row">
      <div class="lc c-6-2">
        <div class="col">
          <div class="title">
            <a target="_blank" href="/catalogue/product/<?=$item->id;?>">
              <?=$item->title;?>
            </a>
          </div>
          <div class="sku">
            <?=$item->sku;?>
          </div>
        </div>
      </div>
      <div class="lc c-6-1 count">
        <?=$custome->rur($item->count);?> шт.
      </div>
      <div class="price lc c-6-1 stop">
        <?=$custome->rur($item->price);?> руб.
      </div>
    </div>
    <? endforeach; ?>
  </div>
  <? endif; ?>
<? endif; ?>