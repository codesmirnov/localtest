<ul class="columns small">
  <? foreach ($sections[0]->children as $section): ?>
    <li>
      <h2><?=$html->link($section->title, $section->url);?></h2>    
      <? if (! empty($section->children)): ?>
      <ul>
        <? foreach ($section->children as $subsection): ?>
          <li>
            <?=$html->link($subsection->title, $subsection->url);?>
            <? if ($subsection->unchecked > 0): ?>
              <sub class="red"><?=$subsection->unchecked;?></sub>
            <? endif; ?>
          </li>
        <? endforeach; ?>
      </ul>
      <? endif; ?>
    </li>
  <? endforeach;?>
</ul>