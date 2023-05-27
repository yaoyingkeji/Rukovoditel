<?php
require(CFG_PATH_TO_PHPSPREADSHEET);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class items_export
{
    public $filename;
    
    private $template_filepath;
    
    function __construct($filename)
    {
        $this->filename = app_remove_special_characters($filename);
        $this->template_filepath = '';
    }
    
    function set_template_filepath($filepath)
    {
        if(is_file($filepath))
        {
            $this->template_filepath = $filepath;
        }
        else
        {
            die(TEXT_FILE_NOT_FOUD . ' ' . $filepath);
        }
    }
    
    function xlsx_from_array($export_data)
    {
        global $app_user;
        
        //load file from template
        if(strlen($this->template_filepath))
        {
            $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
            $spreadsheet = $reader->load($this->template_filepath);
            $spreadsheet->setActiveSheetIndex(0);
        }
        else
        {        
            //create Spreadsheet
            $spreadsheet = new Spreadsheet();

            // Set document properties
            $spreadsheet->getProperties()->setCreator($app_user['name']);
            $spreadsheet->getActiveSheet()->setTitle(TEXT_LIST);    
        }
        
        // Add some data
        $spreadsheet->getActiveSheet()->fromArray($export_data, null, 'A1');
        
        //autosize columns
        $highest_column = $spreadsheet->getActiveSheet()->getHighestColumn();
        
        for ($col = 'A'; $col != $highest_column; $col++)
        {
            $spreadsheet->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getStyle($col.'1')->getFont()->setBold(true);
        }
        
        $spreadsheet->getActiveSheet()->getColumnDimension($highest_column)->setAutoSize(true);
        $spreadsheet->getActiveSheet()->getStyle($highest_column.'1')->getFont()->setBold(true);
        
        // Rename worksheet
        
        
        // Redirect output to a client’s web browser (Xlsx)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . addslashes($this->filename) . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');
        
        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0                
        
        \PhpOffice\PhpSpreadsheet\Shared\File::setUseUploadTempDirectory(true);
        
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');       
    }
}