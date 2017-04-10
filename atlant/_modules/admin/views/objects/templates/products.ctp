<?

  include MODS . 'client' . DS . 'controllers' . DS . 'search.php';

  extract($this->params['url']);

  $items = array();

  if (isset($q)) {
    $search = new Search();
    $items = prep($search->query($q));
  }

?>

<div id="main-search">
  <div class="wrap">
    <form>
      <div class="search-input">
        <input value="<?=$this->params['url']['q'];?>" type="text" name="q" style="color: black">
      </div>
      <div class="button">
        Найти
      </div>
    </form>
    <script type="text/javascript">
      $(function() {
        $('#main-search').each(function() {
          var parent = this;
          $('.button', parent).click(function() {
            $('form', parent).submit();
          })
        })
      })
    </script>
  </div>
</div>

<?

  if (! empty($items)) {
    $files = array();
    $profiles = array();
    $groups = array();
    foreach ($items as $item) {
      if ($item->profile_id > 0) {
        $groups[1][] = $item;
      } else {
        $groups[0][] = $item;        
      }

      $files[$item->file_price_id] = $item->file_price_id;
      $profiles[$item->profile_id] = $item->profile_id;
    }

    $model = new Model(array('table' => 'c_price_files'));
    $files = $model->find('index', array(
      'conditions' => array('id' => $files), 
      'fields'     => array('id', 'title')
    ));

    $model = new Model(array('table' => 's_profiles'));
    $profiles = $model->find('index', array(
      'conditions' => array('id' => $profiles), 
      'fields'     => array('id', 'email')
    ));
  }

?>

<? if (! empty($items)): ?>
<div id="search-result">
  <div class="row">
    <div class="item labels">
      <div class="id-number">ID</div>
      <div class="number">Номер</div>
      <div class="title">Название</div>
      <div class="count">В наличии</div>
      <div class="time">Срок поставки</div>
      <div class="price"><div>Цена за шт. руб.</div></div>
    </div>
    <? 
      $margin = false;
      $prev = '';
    ?>
    <? foreach ($groups as $i => $items):?>
      <? if ($i >= 1): ?>
        <div class="subtitle-row">
          Предложения партнеров
        </div>
        <div class="item labels">
          <div class="id-number">ID</div>
          <div class="number">Номер</div>
          <div class="title">Название</div>
          <div class="count">В наличии</div>
          <div class="time">Срок поставки</div>
          <div class="price"><div>Цена за шт. руб.</div></div>
        </div>
      <? endif; ?>
        <? foreach ($items as $item): ?>
          <? if ($margin && $item->sku != $prev): ?>
            <div class="item">
              <div class="empty">
              </div>
            </div>
          <? endif; ?>
          <div class="item">
            <div class="id-number">  
              <a href="<?=$html->href($item->id);?>" >
                <?=$item->id;?>                
              </a>
            </div>
            <div class="number">
              <a href="?q=<?=$item->sku;?>" class="<?=$item->sku == $prev ? ' second' : '';?>">
                <?=$item->sku;?>                
              </a>
            </div>
            <div class="title">
              <?=$html->a($item->title,$item->id);?>
              <div class="file-price">
                <? if ($item->file_price_id > 0): ?>
                <span>  &rarr; </span>
                <?=$html->a($files[$item->file_price_id],'/admin/content/prices/' . $item->file_price_id);?>
                <? endif; ?>
                <? if ($item->profile_id > 0): ?>
                <span>  &rarr; </span>
                <?=$html->a($profiles[$item->profile_id],'/admin/services/profiles/' . $item->file_price_id);?>
                <? endif; ?>
              </div>
            </div>
            <div class="count"><?=$item->available;?>  <?=$item->unit;?></div>
            <div class="time">
              <pie style="display: none" class="ten"></pie>
              <span class="delivery-time">
                <?=calendarDay($item->delivery);?> дн
              </span>
            </div>

            <div class="price">            
              <?=$custome->rur($item->price);?>
            </div>
          </div>

          <? 
            $margin = $item->sku == $prev;
            $prev = $item->sku;
          ?>
        <? endforeach; ?>
    <? endforeach;?>
  </div>
</div>
<? endif; ?>