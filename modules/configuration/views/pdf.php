<h3 class="page-title"><?php echo TEXT_PDF_EXPORT_FONTS ?></h3>
<p><?php echo TEXT_PDF_EXPORT_FONTS_INFO ?></p>

<?php
$rootDir = $fontDir = '';
$fonts_list = json_decode(file_get_contents(CFG_PATH_TO_DOMPDF_FONTS . '/installed-fonts.json'),true);

$fonts_list = is_array($fonts_list) ? $fonts_list : [];

//print_rr($fonts_list);

echo button_tag(TEXT_ADD, url_for('configuration/pdf_form'), true);
?>

<div class="table-scrollable">
<table class="table table-striped table-bordered table-hover">
<thead>
  <tr>
    <th><?php echo TEXT_NAME ?></th>        
    <th><?php echo TEXT_FILENAME ?></th>        
  </tr>
</thead>
<tbody>
    <?php
        foreach($fonts_list as $font_name=>$font_types)
        {
            echo '
                <tr>
                    <td>' . $font_name . '</td>
                    <td>'  . str_replace('//','/',$font_types['normal']) . '</td>
                </tr>
                ';
        }
    ?>    
</tbody>
</table>
</div>
<?php 
    echo TEXT_FONTS_FOLDER . ': ' . CFG_PATH_TO_DOMPDF_FONTS
?>
