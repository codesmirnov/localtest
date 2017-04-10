<? if (isset($render) && $render): ?>
  <?
    if ($this->data['id']) {
      $model = new Model(array('table' => 'c_products'));
      $items = prep($model->find('all', array(
        'limit' => 500,
        'conditions' => array('file_price_id' => $this->data['id']))));
    }
  ?>

  <? if (! empty($data)): ?>
    <table class="decor">
      <tr>
        <th>ID</th>
        <th>Брэнд</th>
        <th>Артикул</th>
        <th>Название</th>
        <th>Цена (руб.)</th>
        <th>Цена с наценкой (руб.)</th>
        <th>Количество</th>
        <th>Срок поставки</th>
      </tr>
    <? foreach ($items as $item): ?>
      <tr<?=!$item->is_public ? ' class="is_hidden"' : '';?>>        
        <td><a href="/admin/content/products/<?=$item->id;?>"><?=$item->id;?></a></td>
        <td><?=$item->brand;?></td>
        <td><a href="/admin/content/products/<?=$item->id;?>"><?=$item->sku;?></a></td>
        <td><a href="/admin/content/products/<?=$item->id;?>"><?=$item->title;?></a></td>
        <td><?=$item->dry_price;?></td>
        <td><?=$item->price;?></td>
        <td><?=$item->count;?> <?=$item->unit ? $item->unit : 'шт';?>.</td>
        <td><?=$item->delivery;?> дн.</td>
      </tr>
    <? endforeach; ?>
    </table>
  <? endif; ?>
<? endif; ?>