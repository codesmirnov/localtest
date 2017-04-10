<?

if (! function_exists('__imagesBeforeSave')) {
  function __imagesBeforeSave($value) {
    if (! empty($value)) {
      $value  = explode(',', $value);
      $images = array();
      
      foreach ($value as $id) {
        $images[] = array(
          'id' => $id
        );
      }
      
      return $images;
    } else
      return array();
  }
}

?>

<? if (isset($_setup) && $_setup): ?>
  <div class="input">
    <label>Таблица</label>
    <?=$form->hidden('Params.join', array('value' => 'hasMany')); ?>
    <?=$form->hidden('Params.join_order', array('value' => 'pos')); ?>
    <?=$form->input('Params.table', array('type' => 'text', 'default' => 'c_media', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Ключ</label>
    <?=$form->input('Params.join_key', array('type' => 'text', 'default' => '', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Поля</label>
    <?=$form->input('Params.fields', array('type' => 'text', 'default' => 'id, title'));?>
  </div>
  <div class="input">
    <label>Сортировка</label>
    <?=$form->input('Params.order', array('type' => 'text', 'default' => 'pos', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Директория</label>
    <?=$form->input('Params.path', array('type' => 'text'));?>
  </div>
<? elseif (isset($render) && $render): ?>
  <?=$html->resource(array(  
  	"/_modules/admin/js/swf/swfupload.js",
  	"/_modules/admin/js/swf/swfupload.swfobject.js",
  	"/_modules/admin/js/swf/swfupload.queue.js",
  	"/_modules/admin/js/swf/fileprogress.js",
  	"/_modules/admin/js/swf/handlers.js",
  	'/_modules/admin/js/jquery.swfupload.js'
  )); ?>
  
  <div id="photo-uploader" class="input swf-uploader" extension="<?=$params->extension;?>" phpsessid="<?=session_id();?>" swf="/_modules/admin/js/swf/swfupload.swf">
    <?=$form->hidden($field->alias);?>
    <div class="progress"></div>
    <div class="holder"></div>
    <?=$html->link('Загрузить', $this->params['admin']['url'] . '/_images/' . $field->id . '/', array('class' => 'target java'));?>
    <div class="wrap"> 
      <? if (! empty($_data->{$field->alias})): ?>
        <? foreach ($_data->{$field->alias} as $i): ?>
          <img src="<?=$image->crop($i->path, 105, 105);?>" alt="<?=$i->id;?>" title="<?=$i->id;?>" itemid="<?=$i->id;?>" />
        <? endforeach; ?>
      <? endif; ?>
    </div>
    <p>
      Первое фото будет использовано для предпросмотра в ленте объявлений<br/>
      Вы можете менять фотографии местами просто перетаскивая их мышкой<br/>
      Чтобы удалить фотографии, перетащите ее за предлеы рамки
    </p>
  </div>
<? endif; ?>