<?=$html->resource(array(
  '/_modules/admin/js/jcrop/css/jquery.Jcrop.css',
  '/_modules/admin/js/jcrop/js/jquery.Jcrop.min.js'
));?>

<fieldset>
  <h3>Картинка</h3>
  <div class="reducer">
    <div class="input">
      <input type="submit" value="Обрезать" /> &nbsp; <a href="#">Восстановить оригинал</a>
    </div>
    <img id="target-image" src="<?=$img->path;?>" style="max-width: 100%;" />
  </div>
</fieldset>


<script>
  $(function() {
    $('#target-image').Jcrop();
  })
</script>