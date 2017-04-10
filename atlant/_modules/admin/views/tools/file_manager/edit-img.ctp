<p>
	<? $info = getimagesize($path); ?>
	<b>Размеры: </b> <?=$info[0] . '&times;' . $info[1]; ?><br/>
	<b>Расширение: </b> <?=$info['mime'];?>
</p>
<div style="width: 100%; text-align: center">
	<img src="<?=$src;?>" />
</div>