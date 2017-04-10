<div class="controll-object-table controll-object-edit" href="<?=$this->params['current']['url'];?>/">
	<div class="controlls">
		С отмеченными:
		<span id="show" class="java">Опубликовать</span>,
		<span id="hide" class="java">Скрыть</span>
		<span id="delete" class="java" style="color: red"><b>&times;</b> Удалить</span>
	</div>
	<table class="decor table-dnd">
		<tr class="nodrop nodrag">
			<th class="controll"><input id="select-all" type="checkbox" class="controll" /></th>
      <th></th>
      <th>Артикул</th>
      <th style="width:79px" >Цвет</th>
      <th>Название</th>
      <th>Описание</th>
      <th style="width:59px" >Цена</th>
      <th>Сезон</th>
      <th>Коллекция</th>
		</tr>
		<? foreach($items as $item): ?>
			<tr<?=! $item->is_public ? ' class="is_hidden"' : '';?> itemid="<?=$item->id;?>">
				<td class="controll"><input type="checkbox" class="controll" /></td>
        <td><img src="<?=$image->resize($item->Photos[0]->path, 127, 168);?>" id="<?=$photo->id;?>" pos="<?=$photo->pos;?>" /></td>
        <td><?=$html->a($item->sku, $item->id);?></td>
        <td><input name="color" type="text" value="<?=$item->color;?>" /></td>
        <td><input name="title" type="text" value="<?=$item->title;?>" /></td>
        <td><textarea name="notice"><?=$item->notice;?></textarea></td>
        <td><input name="price" type="text" value="<?=$item->price;?>" /></td>
        <td><input name="season" type="text" value="<?=$item->season;?>" /></td>
        <td><input name="collection" type="text" value="<?=$item->collection;?>" /></td>
			</tr>
		<? endforeach; ?>
	</table>
</div>

<script>
  $(function() {
    $('.controll-object-edit').each(function() {
      var parent = this;
      var href   = $(this).attr('href');
      
      $('input, textarea').change(function() {
        var item  = this;
        var id    = $(this).parents('tr').attr('itemid');
        var field = $(this).attr('name');
        var value = $(this).val();
        
        var items = new Array();
        
        items[0] = {id : id , field : field, value : value};
        
        $.post(href + 'fields', {items : items}, function() {
          $(item).addClass('success');
        })        
      })
    })
  })
</script>