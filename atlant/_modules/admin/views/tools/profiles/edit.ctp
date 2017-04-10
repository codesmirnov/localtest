<?=$form->create('Profile');?>
  <?=$form->hidden('id');?>
  <fieldset>
    <h3>Основное</h3>
    <div class="reducer">
      <div class="input">
        <label>Логин</label>
        <?=$form->input('login');?>
      </div>
      <div class="input">
        <label>Пароль</label>
        <?=$form->input('password', array('type' => 'password'));?>
      </div>
      <div class="input">
        <label>Имя</label>
        <?=$form->input('name');?>
      </div>
      <div class="input submit">
        <input type="submit" value="Сохранить" /> 
        &nbsp; <?=$html->link('Удалить', 'del', array('class' => 'java delete-link'));?>
      </div>
    </div>
  </fieldset>
  <fieldset class="one-col">
    <h3>Доступ к разделам</h3>
    <?=$form->hidden('access');?>
    <?     
      $map = new Model(array(
        'table' => '_sys_map'
      ));
      
      $map = $map->find('threaded');      
    ?>
    
    <? $html->start('li'); ?>
      <li id="_section-<%=$item['id'];%>">
        <input type="checkbox" />
        <span class="java" itemid="<%=$item['id'];%>"><%=$item['title'];%></span>
        <% if ($item['children']): %>
          <%=$html->template('ul', array('items' => $item['children']));%>
        <% endif; %>
      </li>
    <? $html->end(); ?>
    
    <? $html->start('ul'); ?>
      <ul<%=isset($class)?' class="' . $class . '"':'';%>>
        <% foreach ($items as $item): %>
          <%=$html->template('li', array('item' => $item));%>
        <% endforeach; %>
      </ul>
    <? $html->end(); ?>
    
    <?=$html->template('ul', array('items' => $map, 'class' => 'tree tree-checkbox select-ban'));?>
    
    <script>
      $('.tree-checkbox').each(function() {
        var parent = this;
        
        function serialize() {
          var list = '';
          $('input:checked', parent).each(function() {
            list += $(this).next().attr('itemid') + ';';
          })
          
          $('#_access').val(list);
        }
        
        $('.java', this).click(function() {
          var parent   = $(this).parent();
          var checked  = ! $(this).prev().attr('checked');
          $('input', parent).attr('checked', checked);
          
          $(parent).parents('li').each(function() {
            if ($('ul input:checked', this).size()) {
              $('input:first', this).attr('checked', true);
            } else
              $('input:first', this).attr('checked', false);
          });
          
          serialize();
        })
        
        var list = $('#_access').val().split(';');
        for (i in list) {
          $('#_section-' + list[i] + ' input:first').attr('checked', true);
        }
      })
    </script>
  </fieldset>
<?=$form->end();?>