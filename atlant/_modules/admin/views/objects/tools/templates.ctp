<? if (count($templates) > 1): ?>
  <div class="filter left b">
    <i>Режим показа:</i>
    <? foreach ($templates as $i => $template): ?>
      <?=$html->paramLink($template->name, 'template=' . $i);?>
    <? endforeach; ?>
  </div>
<? endif; ?>