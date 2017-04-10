<div class="filters">
  <?=$this->element('tools/templates');?> 
  <?=$this->element('tools/categories');?>
  <?=$this->element('tools/filters');?>
  <div style="clear: both;" ></div>
</div>

<? $paginator = $custome->paginator(array('mode' => 'paramLink', 'label' => 'Страницы', 'class' => 'paginate tools')); ?>

<?=$paginator;?>

<?=$this->element('templates' . DS . str_replace('.ctp', '', $templateFile)); ?>

<?=$paginator;?>