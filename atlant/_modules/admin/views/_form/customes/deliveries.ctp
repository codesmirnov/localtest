<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>

<?        
  $delivery = Configure::read('deliveries'); 
?>

<div class="input">
  <label>Способо доставки</label>
  <?=$form->input('delivery', array('type' => 'select', 'options' => $delivery)); ?>
</div>

<? endif; ?>