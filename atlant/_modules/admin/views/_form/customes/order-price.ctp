<? if (isset($_setup) && $_setup): ?>
<? elseif (isset($render) && $render): ?>

<?
  $discount = $this->data['Profile']['discount'];  
?>

<div class="input">
  <label>Сумма к оплате</label>
  <?=$form->input('price', array('type' => 'text', 'style' => 'width: 59px')); ?> руб.
  <? if ($discount > 0): ?>
    <p class="red">
      С учетом персональной скидки в <?=$discount;?>%
    </p>
  <? endif; ?>
</div>

<? endif; ?>