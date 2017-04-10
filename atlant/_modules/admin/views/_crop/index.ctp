<div class="filters">
  <div class="filter">
    <i>Размеры:</i>
    <? foreach ($sizes as $size): ?>
      <?=$html->paramLink($size, 'size=' . $size); ?>
    <? endforeach; ?>
  </div>
  <div style="clear: both;" ></div>
</div>

<div id="imagecrop" href="<?=$html->href();?>">
  <? if (! empty($images)): ?>
    <? foreach ($images as $image): ?>
      <div class="image" style="overflow: hidden; display: inline-block; cursor: pointer; border: 1px solid gray;" original="<?=$image->original;?>">
        <img class="original" src="" style="display: none; position: relative; cursor: move"/>
        <img class="thumb" src="<?=$image->cache;?>" />
      </div>
    <? endforeach; ?>
  <? endif; ?>
</div>

<script>
  $(function() {
    
    $('.image').click(function() {      
      $(this).width($(this).width());
      $(this).height($(this).height());
      
      var img      = $('img.original', this);
      var thumb    = $('img.thumb', this);
      var original = $(this).attr('original');
      var href     = $('#imagecrop').attr('href');
      
      $.post(href, {'original' : original}, function(data) {
        data = $.parseJSON(data);
        
        thumb.hide();
        img.attr('src', original).width(data[0]).height(data[1]).css('left', -data[2] + 'px').css('top', -data[3] + 'px').show();
        
        img.draggable({
          axis : 'y',
          start: function(event, ui) {
            
          },
          stop : function(event, ui) {
            var top  = ui.position.top;
            var left = ui.position.left;
            var src  = ui.helper.attr('src');
            $.post(href, {'act' : 'correct', 'cropY' : top, 'cropX' : 0, 'rWidth' : data[0], 'rHeight' : data[1], 'original' : src, 'cache' : thumb.attr('src')});
          }
        });
      })            
    })    
  })  
</script>