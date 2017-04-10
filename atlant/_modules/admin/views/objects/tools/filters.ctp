<? $params = $this->params['current']['Params']; ?>
<? if (! empty($params['filters'][0]['field'])): ?>
  <div class="filter _checkbox-filter right">
    <i>Фильтр:</i>
    <? foreach ($params['filters'] as $filter): ?>
      <div class="checkbox r">
        <div name="<?=$filter['field'];?>" id="_filter-<?=md5($filter['field']);?>" class="three-checkbox<?=$filter['checked'] ? ' checked' : '';?><?=$filter['unchecked'] ? ' unchecked' : '';?><?=$filter['value2'] != '' ? ' value2' : '';?>"></div>
        <label for="_filter-<?=md5($filter['field']);?>"><?=$filter['name'];?></label>
      </div>
    <? endforeach; ?>
  </div>
<? endif; ?>

