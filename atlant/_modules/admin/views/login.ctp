<?=$form->create('Profile'); ?>
<fieldset>
  <div class="reducer">
    <div class="input">
      <label>Логин</label>
      <?=$form->input('login');?>
    </div>
    <div class="input">
      <label>Пароль</label>
      <?=$form->input('password', array('type' => 'password'));?>
    </div>
    <div class="input submit">
      <input type="submit" value="Войти" />
    </div>
  </div>
</fieldset>
<?=$form->end(); ?>