<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="ru" xml:lang="ru">
<head>
  <?=$html->resource('/_modules/admin/css/style.css'); ?>
  
  <?=$html->resource('/_modules/admin/js/jquery.js'); ?>
  <?=$html->resource('/_modules/admin/js/jquery-ui.min.js'); ?>
  <?=$html->resource('/_modules/admin/js/jquery.ajax-form.js'); ?>   
  <?=$html->resource('/_modules/admin/js/dropdown.js'); ?>    
  <?=$html->resource('/_modules/admin/js/jquery.alert.js'); ?>    
	<?=$html->resource('/_modules/admin/js/jquery.tablednd_0_5.js', 'js'); ?>
  <?=$html->resource('/_modules/admin/js/scripts.js'); ?>
  
  <?=$html->resources(); ?>
  
  <title><?=$this->params['current']['title'];?></title>
</head>
<body>
	<div id="layer">
    <? if (isset($sections[0])): ?>
    <div class="layout-reducer">
  		<div id="header">	
  			<div class="filter row">
  	      <i>Разделы:</i>   
          
          <? foreach ($sections[0]->children as $item): ?>
            <?=$html->link($item->title, $item->url); ?>
            <? if ($item->unchecked > 0): ?>
              <sub class="red"><?=$item->unchecked;?></sub>
            <? endif; ?>
          <? endforeach; ?>	  
          
          <div class="profile-block">
            <?=$html->a($logProfile->name, '/admin/tools/profiles/' . $logProfile->id);?> | <a href="/logout">Выйти</a>
          </div>
        </div>
        <div class="row">
    	    <p class="breadcrumbs">
            <?=$html->crumbs(); ?>          
          </p>
          <div class="input search">
            <label>Поиск</label>            
            <div id="_atlant-search" class="suggest selected">
            	<div class="get">
            		<input type="text" value="" /> <i>▼</i>
            	</div>
            	<div class="list wrapper">
            		<ul class="ajax">
                </ul>
            	</div>
            </div>
          </div>
        </div>
      </div>    
    </div>
    <? endif; ?>
    
    <div class="layout-reducer trim">
      <h1 class="main-title">
				<?=$this->params['current']['title'] . (isset($this->params['current']['title']) && ! empty($this->params['jump']) ? ' &rarr; ' . $html->link($this->params['jump'][0], $this->params['jump'][1]) : '');?>
        <? if (isset($setupLink) && $setupLink): ?>
        <?=$html->link('Настроить', '_setup', array('class' => 'setup-link'));?>
        <? endif; ?>
      </h1> 
	    <div id="content">
	      <?=$content_for_layout; ?>
	    </div>
			<div class="layout-help" style="display: none;">
			</div>
		</div>   
    
    <div id="footer" style="clear:  both;">
      <div class="reducer">
        <span class="copyright">© 2011<?=date('Y') > 2011 ? ' - ' . date('Y') : '';?> ДВА-Д</span>
      </div>
    </div>
  </div>
</body>
</html>