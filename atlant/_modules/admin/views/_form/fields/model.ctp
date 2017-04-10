<? if (isset($_setup) && $_setup): ?>
  <div class="input">
    <label>Таблица</label>
    <?=$form->input('table', array('type' => 'text', 'default' => '', 'style' => 'width: 121px'));?>
  </div>
  <div class="input">
    <label>Тип связи</label>
    <?=$form->input('join', array('type' => 'select', 'options' => array('belongsTo', 'hasOne', 'hasMany')));?>
  </div>
  <div class="input">
    <label>Условия выборки</label>
    <?=$form->input('conditions', array('type' => 'text'));?>
    <p>Условия mysql</p>
  </div>
  <div class="input">
    <label>Поля</label>
    <?=$form->input('conditions', array('type' => 'text'));?>
    <p>Необходимые поля через запятую</p>
  </div>
  <div class="input">
    <label>Лимит</label>
    <?=$form->input('conditions', array('type' => 'text', 'style' => 'width: 31px'));?>
    <p>Ограничить выборку до указанного числа</p>
  </div>
<? elseif (isset($render) && $render): ?>
<? endif; ?>