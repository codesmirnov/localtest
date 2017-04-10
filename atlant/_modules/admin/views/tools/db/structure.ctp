<? if (! empty($this->data)): ?>

<table class="decor">
	<tr>
		<? foreach ($this->data[0] as $key => $row): ?>
			<? if (isset($need_fields[$key])): ?>
			<th><?=$need_fields[$key];?></th>
			<? endif; ?>
		<? endforeach; ?>
	</tr>
	<? foreach ($this->data as $row): ?>
		<tr>
			<td><?=$html->link($row['Name'], '_' . $row['Name'] , array('class' => 'target')); ?></td>
			<td><?=$html->link($row['Rows'], '_' . $row['Name'] . '/data'); ?></td>
			<td><?=$row['Auto_increment']?></td>
		</tr>
	<? endforeach; ?>
</table>

<? endif; ?>