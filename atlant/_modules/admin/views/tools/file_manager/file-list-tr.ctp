<? if (! empty($files)): ?>
	<? foreach ($files as $file): ?>
		<tr class="file-type-<?=$type;?>">
			<td class="controll"><input type="checkbox" /></td>
			<td>
				<? if (in_array($type, array('jpeg', 'jpg', 'png', 'gif'))): ?>
					<?#$html->image($image->autotrim(array('path' => $relative_path, 'name' => $file), '100x100'));?>
				<? endif; ?>
			</td>
			<td class="name">
				<?=$html->link($file, '_' . $file);?>
			</td>
			<td><?=$type == 'dir' ? 'Директория' : $type;?></td>
		</tr>
	<? endforeach; ?>
<? endif; ?>