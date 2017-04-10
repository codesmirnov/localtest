<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>

<?        
  $statuses = Configure::read('statuses');      
?>

<div class="input">
  <label>Статус заказа</label>
  <?=$form->input('status', array('type' => 'select', 'options' => $statuses)); ?>
</div>

<? endif; ?>