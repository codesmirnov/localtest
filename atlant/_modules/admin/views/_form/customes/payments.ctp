<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>

<?        
  $payments = Configure::read('payments');      
?>

<div class="input">
  <label>Способ оплаты</label>
  <?=$form->input('payment', array('type' => 'select', 'options' => $payments)); ?>
</div>

<? endif; ?>