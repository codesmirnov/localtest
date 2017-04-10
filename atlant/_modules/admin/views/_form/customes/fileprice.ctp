<?

if (! function_exists('__filepriceBeforeSave')) {

  include LIBS . 'PHPExcel.php';
  include LIBS . 'PHPExcel' . DS . 'IOFactory.php';

  function __filepriceBeforeSave($value, $params, &$data) {
		ini_set('display_errors',1);
		ini_set('display_startup_errors',1);
		ini_set('upload_max_filesize', '20M');
		ini_set('memory_limit', '-1');
		ini_set('max_execution_time', '450');
		error_reporting(-1);
	
    if (isset($data['is_delete_products']) && isset($data['is_delete_conf'])) {
      $model = new Model(array('table' => 'c_products'));
      $model->delete(array(
        'file_price_id'  => $data['id']
      ));
    }

    if ($data['id'] > 0 && $value['error'] == 4) {
      $model = new Model(array('table' => 'c_products'));
      $model->update(
        array(
          'is_public'      => $data['is_public'],
          'delivery'       => $data['delivery'],
          'verified:price' => 'dry_price + (dry_price * ' . ($data['markup']/100) . ')'), 
        array('file_price_id'  => $data['id']));
    }

    if ($data['__file'] != '' && file_exists(ROOT . $data['__file'])) {
      $path = $data['__file'];
      exit;
    }

    if ($value['error'] <= 0) {
      $path = fileCopy($value, '/i/contents/files', array(
        'extensions' => array('xls'), 
        'file_name'  => md5($value['name']),
      	'limit'      => 0));
    }

    if ($path != '') {

      if (! $data['id']) {
        $model = new Model(array('table' => 'c_price_files'));
        $data['id'] = $model->save(array('title' => $value['name'], 'is_public' => 1));
      } else {      
        $model = new Model(array('table' => 'c_products'));
        $model->delete(array('file_price_id' => $data['id']));
      }

      $data['title'] = $value['name'];
          
      $file_price_id = $data['id'];
      $profile_id = $data['profile_id'];

      if (! $profile_id) {
        $profile_id = 0;
      }

      $reader = PHPExcel_IOFactory::load(ROOT . $path);

      $fields = "`file_price_id`,`profile_id`,`dry_price`,`price`,`title`,`sku`,`brand`,`available`,`count`,`delivery`,`is_public`";
      $begin  = "INSERT INTO `c_products` ($fields) VALUES ";
      $query  = $begin;

      $dbo = new Dbo();

      $index = 0;
      $rows = array();
      foreach ($reader->getWorksheetIterator() as $worksheet) {

        $rows_count = $worksheet->getHighestRow();
        $columns_count = PHPExcel_Cell::columnIndexFromString($worksheet->getHighestColumn());
        
        for ($row = $columns_name_line + 2; $row <= $rows_count; $row++) {
          $rowData = array();

          for ($column = 0; $column < $columns_count; $column++) {
            $merged_value = "";
            $cell = $worksheet->getCellByColumnAndRow($column, $row);

            foreach ($worksheet->getMergeCells() as $mergedCells) {
              if ($cell->isInRange($mergedCells)) {
                $temp = explode(":", $mergedCells);
                $merged_value = $worksheet->getCell($temp[0]);
                $merged_value = method_exists($worksheet, 'getCalculatedValue') ? $worksheet->getCalculatedValue($merged_value) : '';
                break;
              }
            }
            $rowData[] = (strlen($merged_value) == 0 ? $cell->getCalculatedValue() : $merged_value);
          }


          $price = $dry_price = $rowData[4];

          if ($data['markup']) {
            $price += $price * $data['markup']/100;
          }

          $dry_price     = $dbo->escape($dry_price);
          $price         = $dbo->escape($price);
          $title         = $dbo->escape($rowData[2]);
          $sku           = $dbo->escape($rowData[1]);
          $brand         = $dbo->escape($rowData[0]);
          $count         = $dbo->escape($rowData[3]);
          $delivery      = $dbo->escape($data['delivery']);
          $is_public     = $data['is_public'];

          if (! $price) {
            $price = 0;
          }

          if (! $dry_price) {
            $dry_price = 0;
          }

          if (! $count) {
            $count = 0;
          }

          if (! $title) {
            $title = '\'\'';
          }

          if (! $brand) {
            $brand = '\'\'';
          }

          if (! $sku) {
            $sku = '\'\'';
          }

          $values = "($file_price_id,$profile_id,$dry_price,$price,$title,$sku,$brand,$count,$count,$delivery,$is_public)";  
          $query .= ($index > 0 ? ",\r\n" : "") . $values;
          $index++;

          if ($index > 5000) {
      			$dbo->query($query);
      			$query = $begin;
      			$index = 0;
          }
        }
      }
      
      $dbo->query($query);

      unlink(ROOT . $path);

    }
  }
}

?>

<? if (isset($render) && $render): ?>
  <div class="input file">
    <label for="_<?=$field->alias;?>">Файл-прайс</label>
    <?=$form->input('file', array('type' => 'file')); ?>
    <?=$form->input('__file', array('type' => 'hidden', 'value' => $this->data['file'])); ?>
  </div>
  <div id="fileprice-deletetool">
  <div class="input checkbox r b">
    <input type="checkbox" name="is_delete_products" id="_is_delete_products" value="">    
    <label for="_is_delete_products">Удалить связаные товары</label>
  </div>
  <div class="input checkbox r b">
    <input type="checkbox" name="is_delete_conf" id="_is_delete_conf" value="">    
    <label for="_is_delete_conf">Подтвердить удаление</label>
  </div>
  </div>
<? endif; ?>