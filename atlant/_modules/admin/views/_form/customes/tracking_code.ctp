<?
  if (! function_exists('__tracking_codeBeforeSave')) {    
    function __tracking_codeBeforeSave($value, $params, $data) {
    	if ($data['is_mail_send']) {

        include  MODS . 'client' . DS . 'controllers' . DS . '_client.php' ;

        $client = new Client();
        $client->view = new View($this);
        $client->view->root = MODS . 'client' . DS;
        $client->report(
          'Ваш заказ на Mcbuy.ru №'.$data['number'].' находится в пути', 
          array(
            'code' => $value
          ), 
          'trackingcode',
          $data['email'], 
          'mcbuy.ru <noreply@mcbuy.ru>');

        $data['is_mail_send'] = 0;
    	}
      return $value;
    }      
  }
?>

<? if (isset($render) && $render): ?>
  <div class="input">
    <label>Трекинг код</label>
    <?=$form->input('tracking', array('type' => 'text'));?>
  </div>
  <div class="input checkbox r">
    <?=$form->input('is_mail_send', array('type' => 'checkbox', 'id' => '_is_mail_send'));?>
    <label for="_is_mail_send">Отослать код покупателю</label>
  </div>
<? endif; ?>