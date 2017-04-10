<?=! $this->ajax ? $form->create('Model'): ''; ?>
  <fieldset>
    <h3>Настройки</h3>
    <div class="reducer">
      <div class="input">
        <label>Метод</label>
        <?=$form->input('Params.method', array('type' => 'select', 'options' => array('index', 'object'), 'keys' => true));?>
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
    <script>
      $(function() {        
        $('#_params_method').change(function() {
          $('#_advances .block').hide();
          $('#_advance_' + $(this).val()).show();
        }).each(function() {
          $(this).change();
        })
              
      })
    </script>
  </fieldset>
  <div id="_advances">
    <div id="_advance_object" class="block" style="display: none;">
      <fieldset>
        <h3>Основное</h3>
        <div class="reducer">
          <div class="input">
            <label>Имя модели</label>
            <?=$form->input('Params.model', array('type' => 'select', 'options' => $models));?>
          </div>
          <div class="input r">
            <?=$form->input('Params.title_field', array('default' => 'title', 'style' => 'width: 57px'));?>
            <label>Поле наименования</label>
          </div>
          <div class="input r">
            <?=$form->input('Params.url_field', array('default' => 'url', 'style' => 'width: 57px'));?>
            <label>Поле ссылки &laquo;перейти&raquo;</label>
          </div>
          <div class="input submit">
            <input type="submit" value="Сохранить" /> 
            &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <h3>Шаблоны</h3>
        <div class="reducer">
          <? $templates = array_diff(scandir($this->root . 'views' . DS . $this->viewPath . DS . 'templates'), array('.', '..')); ?>
          <?=$form->tableInputs(
        		'Params.templates',
        		array(
        			'name'     => 'Имя шаблона',
        			'file'     => array('th' => 'Файл шаблона', 'type' => 'select', 'options' => $templates, 'keys' => true),
              'order'    => array('th' => 'Сортировка', 'style' => 'width: 150px'),
              'limit'    => array('th' => 'Лимит', 'style' => 'width: 30px'),              
              'paginate' => array('th' => 'Постраничный вывод', 'type' => 'checkbox')
        		),
        		array('class' => 'decor table-inputs')
        	);
          ?>
          <div class="input submit">
            <input type="submit" value="Сохранить" /> 
            &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
          </div>
        </div>  
      </fieldset>
      <fieldset>
        <h3>Выборка</h3>
        <div class="reducer">
          <div class="input">
            <label>Метод выборки</label>
            <?=$form->input('Params.find_method', array('type' => 'select', 'options' => array('all', 'threaded'), 'keys' => true));?>
          </div>
          <div class="input">
            <label>Условие</label>
            <?=$form->input('Params.conditions');?>
          </div>
          <div class="input">
            <label>Сортировка</label>
            <?=$form->input('Params.order', array('style' => 'width: 93px'));?>
          </div>
          <div class="input submit">
            <input type="submit" value="Сохранить" /> 
            &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <h3>Каталог</h3>
        <div class="reducer">
          <div class="input">
            <?=$form->input('Params.category.show', array('type' => 'checkbox')); ?>
            <label>Выводить каталог</label>
          </div>
          <div class="input">
            <label>Таблица</label>
            <?=$form->input('Params.category.table', array('style' => 'width: 93px'));?>
          </div>
          <div class="input">
            <label>Ключ</label>
            <?=$form->input('Params.category.key', array('style' => 'width: 93px'));?>
          </div>
          <div class="input submit">
            <input type="submit" value="Сохранить" /> 
            &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
          </div>
        </div>
      </fieldset>
      <fieldset>
        <h3>Фильтры</h3>
        <div class="reducer">
          <?=$form->tableInputs(
        		'Params.filters',
        		array(
        			'name'  => 'Метка',
        			'field'  => 'Поле',
              'value'  => 'Значение',
              'value2' => 'Значение 2'
        		),
        		array('class' => 'decor table-inputs')
        	);
          ?>
          <div class="input submit">
            <input type="submit" value="Сохранить" /> 
            &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
          </div>
        </div>
      </fieldset>
    </div>
  </div>
<?=! $this->ajax ? $form->end() : '';?>  