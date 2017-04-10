<?=$form->create($_model->alias, array('enctype' => 'multipart/form-data')); ?>
  <?=$form->hidden('id');?>
  <? if (isset($_data->parent_id)): ?>
    <?=$form->hidden('parent_id');?>
  <? endif; ?>
  <?
    foreach ($_model->Groups as $group) {
      $fields = '';
      if (! empty($group->Fields)) {
        foreach ($group->Fields as $field) {
          $fields .= $this->render('fields/' . str_replace('.ctp', '', $field->file), '', array('field' => $field, 'params' => json_decode($field->params), 'render' => true), '_form');
        }
        echo $this->render('groups/' . str_replace('.ctp', '', $group->file), '', array('fields' => $fields, 'group' => $group), '_form');
      }
    }
  
  ?>

<?=$form->end(); ?>