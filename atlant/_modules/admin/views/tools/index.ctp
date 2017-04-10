<ul class="columns">
  <? foreach ($tools as $tool): ?>
	<li>
		<h2>
      <?=$html->link($tool->title, $tool->url); ?>
    </h2>
    <? if (! empty($tool->children)): ?>
      <ul>
        <? foreach ($tool->children as $item): ?>
          <li><?=$html->link($item->title, $item->url); ?></li>
        <? endforeach; ?>
      </ul>
    <? endif; ?>
	</li>
  <? endforeach; ?>
</ul>