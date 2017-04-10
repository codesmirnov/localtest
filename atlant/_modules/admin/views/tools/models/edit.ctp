<div id="model-edit-wrap">
  <?=$form->create('Model');?>
    <?=$form->hidden('id');?>
    <fieldset>
      <h3>Модель</h3>
      <div class="reducer">
        <div class="input">
          <label>Имя</label>
          <?=$form->input('name');?>
        </div>
        <div class="input">
          <label>Псевдоним</label>
          <?=$form->input('alias');?>
        </div>
        <div class="input">
          <label>Таблица</label>
          <?=$form->input('table');?>
        </div>
        <div class="input submit">
          <input type="submit" value="Сохранить" /> 
          &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
        </div>
      </div>
    </fieldset>
  <?=$form->end();?>
  <? if (! $_data->id): ?>
    <fieldset class="one-col">
      <h3>Группы полей данных</h3>
      <p class="red">Перед тем как начать работу с полями данных, нужно сохранить модель</p>
    </fieldset>
  <? else: ?>
  <div href="<?=$ROOT;?>" class="_atlant-groups-fields _atlant-sideform groups" id="_model-field-edit">
  	<h3>Группы полей данных → 
      <a class="group-add is-group java" href="<?=$html->href($ROOT . '/groups/add/'.$_data->id);?>">Добавить группу</a>, 
      <span class="copy disabled"><b></b>Копировать</span>, 
      <span class="paste<?=isset($_SESSION['models']['clipboard']) ? '' : ' disabled';?>"><b></b>Вставить</span>
    </h3>
  	<div class="spacer ui-draggable">
  		<div class="workspace" style="width: 1823px;">
  			<div action="<?=$html->href($ROOT . '/groups/');?>" class="groups ui-sortable checkbox">
          <? if (! empty($_data->Groups)): ?>
            <? foreach ($_data->Groups as $group): ?>
    				<div itemid="<?=$group->id;?>" class="group">
    					<h3 class="handle">
      					<input class="group-checkbox" itemid="<?=$group->id;?>" type="checkbox" /> 
                <?=$html->a($group->name, $ROOT . '/groups/'.$group->id, array('class' => 'java'));?>
                <?=$html->a('+', $ROOT . '/fields/add/'.$group->id, array('class' => 'java add'));?>
              </h3>
    					<div class="fields">
    						<div action="<?=$html->href($ROOT . '/fields/');?>" class="wrap ui-sortable">
                  <? if (! empty($group->Fields)): ?>
                    <? foreach ($group->Fields as $field): ?>
      							<div itemid="<?=$field->id;?>" class="field">
                      <input class="field-checkbox" itemid="<?=$field->id;?>" type="checkbox" />
      								<?=$html->a($field->name, $ROOT . '/fields/'.$field->id, array('class' => 'java'));?>
      							</div>
                    <? endforeach; ?>
                  <? endif; ?>
    						</div>
    					</div>
    				</div>
            <? endforeach; ?>
          <? endif; ?>
  			</div>
  		</div>
  		<div class="sideform">
  			<div class="tabs-nav handler wrap">
  				<span class="l back">← <span class="java">Назад к модели данных</span></span>
  				<span class="m current"></span>
  			</div>
  			<div class="target wrap bk">
  			</div>
  		</div>
  	</div>
  </div>
  <script>
    $('#_model-field-edit').each(function() {
      var parent = this;
      
      $('.copy', parent).click(function() {
        if ($(this).hasClass('disabled'))
          return false;
          
        var fields = new Array();
        var groups = new Array();
        
        $('input[type=checkbox]', parent).each(function() {
          if ($(this).is(':checked')) {
            if ($(this).hasClass('field-checkbox')) {
              fields.push($(this).attr('itemid'));
            } else
            if ($(this).hasClass('group-checkbox')) {
              groups.push($(this).attr('itemid'));            
            }
          }
        })
        
        $.post($(parent).attr('href') + '/copy', {'fields' : fields, 'groups' : groups}, function() {
          
        });
      })
      
      $('.paste', parent).click(function() {        
        if ($(this).hasClass('disabled'))
          return false;
        
        $.post($(parent).attr('href') + '/paste?model_id=' + $('#_id').val(), {}, function() {
          document.location.href = document.location.href;
        });
      })
      
      $('input[type=checkbox]', parent).click(function() {
        if ($('input[type=checkbox]:checked', parent).size()) {
          $('.copy', parent).removeClass('disabled');
        } else
          $('.copy', parent).addClass('disabled');
      })
    })
  </script>
  <? endif; ?>
</div>