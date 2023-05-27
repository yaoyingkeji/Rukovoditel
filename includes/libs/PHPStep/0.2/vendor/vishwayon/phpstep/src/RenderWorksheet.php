<?php

/**
 * @link http://www.vishwayon.com/
 * @copyright Copyright (c) 2020 Vishwayon Software Pvt Ltd
 * @license MIT
 */

namespace PhpStep;

use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpStep\base\PatternType;

/**
 * RenderWorksheet puts together the Xlsx template worksheet and the json data source
 * This class applies the Json data to the worksheet
 * .
 * All changes are reflected in the original template file. Make sure that you 
 * have created a copy of the original file before submission or use the writer after
 * applying template and save the file with a new name
 * 
 * @author girish
 */
class RenderWorksheet
{

    /**
     * Contains a collection of regex patterns to search
     * @var array
     */
    private $patterns = [
        'field' => '/\$\{\S{1,}\}/', // Field with pattern $F{field_name}
        'each' => '/\$table\{\S{1,}\}/' // Each with pattern $Each{array_name}
    ];

    /**
     * Applies data to the requested worksheet
     * Data should always be a model with accessible properties
     * 
     * @param Worksheet $worksheet      The worksheet template
     * @param mixed $data               A data structure/model that contains properties to be applied to the worksheet
     */
    public function applyData(Worksheet $worksheet, $model)
    {
        $hRow = $worksheet->getHighestRow();
        $hCol = Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        for($row = 1; $row <= $hRow; $row++)
        {
            for($col = 1; $col <= $hCol; $col++)
            {
                $cell = $worksheet->getCellByColumnAndRow($col, $row);
                $ptype = $this->parsePattern($worksheet, $cell);
                
                                                
                if($ptype->getType() == PatternType::PATTERN_TYPE_FIELD)
                {
                    $this->setCellData($cell, $ptype, $model);
                }
                elseif($ptype->getType() == PatternType::PATTERN_TYPE_EACH)
                {
                    //Store Each Row marker
                    $eachRowMarker = $row;
                    $row++;
                    if(property_exists($model, $ptype->propName))
                    {
                        $prop = $ptype->propName;
                        $childItems = $model->$prop;
                        
                        if(!is_array($childItems))
                        {
                            continue;
                        }
                        
                        foreach($childItems as $itm)
                        {
                            // insert row in sheet
                            $worksheet->insertNewRowBefore($row, 1);

                            //print_rr($ptype->tranInfo);

                            foreach($ptype->tranInfo as $cc => $cptype)
                            {
                                $cell = $worksheet->getCellByColumnAndRow($cc, $row);

                                $this->setCellData($cell, $cptype['type'], (object) $itm);
                                // Copy cell styles to inserted row
                                $worksheet->duplicateStyle($worksheet->getStyleByColumnAndRow($cc, $row + 1), Coordinate::stringFromColumnIndex($cc) . $row);

                                if($cptype['range'] and $cptype['type']->getType() == PatternType::PATTERN_TYPE_FIELD)
                                {
                                    if(preg_match('/([A-Z]+)(\d+):([A-Z]+)(\d+)/', $cptype['range'], $matches))
                                    {
                                        //echo  $row;
                                        //print_rr($matches);
                                        $worksheet->mergeCells($matches[1] . $row . ':' . $matches[3] . $row);
                                    }
                                }
                            }
                            $row++;
                        }
                        // Remove row->field markers
                        $worksheet->removeRow($eachRowMarker);
                        $worksheet->removeRow($row - 1);
                        $row--;
                    }
                }
                $hRow = $worksheet->getHighestRow();
            }
        }
    }

    /**
     * Returns the patternType from the cell
     * @param Cell $cell
     * @return string
     */
    private function parsePattern(Worksheet $worksheet, Cell $cell): PatternType
    {
        $val = $cell->getValue();
        $pType = new PatternType(PatternType::PATTERN_TYPE_NONE);
        if(isset($val) and preg_match($this->patterns['field'], $val, $matched))
        {            
            $pType = new PatternType(PatternType::PATTERN_TYPE_FIELD);
            
            preg_match_all($this->patterns['field'], $val, $matches);
            
            //print_rr($matches);
            $pType->propNameList = [];
            
            if(count($matches[0])>1)
            {                
                foreach($matches[0] as $matched)
                {                
                    $pType->propNameList[] = strtr($matched, [
                        '${' => '', '}' => ''
                    ]);                                
                }                                
            }
            else
            {
                $pType->propName = strtr($matches[0][0], [
                        '${' => '', '}' => ''
                    ]);  
            }
            
            
            
            return $pType;
        }
        elseif(isset($val) and preg_match($this->patterns['each'], $val, $matched))
        {
            $pType = new PatternType(PatternType::PATTERN_TYPE_EACH);
            $pType->propName = strtr($matched[0], [
                '$table{' => '', '}' => ''
            ]);
            $pType->tranInfo = $this->buildTranInfo($worksheet, $cell->getRow());
        }
        elseif($val !== null && isset($val))
        {
            $pType->currentValue = $val;
        }
        return $pType;
    }

    private function setCellData(Cell $cell, PatternType $ptype, $model)
    {                       
        if($ptype->getType() != PatternType::PATTERN_TYPE_NONE )
        {
            $prop = $ptype->propName;
            $cellValue = $cell->getValue();                        
                                          
            if($cellValue)
            {
                if(count($ptype->propNameList))
                {                    
                    foreach($ptype->propNameList as $prop)
                    {
                        if(property_exists($model, $prop))
                        {
                            
                            $value = str_replace('${' . $prop . '}', $model->$prop, $cellValue);
                            $cell->setValue($value);  
                            $cellValue = $cell->getValue();
                        }
                    }
                }
                elseif(property_exists($model, $ptype->propName))
                {
                    $value = str_replace('${' . $prop . '}', $model->$prop, $cellValue);
                    $cell->setValue($value);
                }
            }
            elseif(strlen($prop) and property_exists($model, $prop))
            {                
                $cell->setValue($model->$prop);
            }
        }
        elseif($ptype->getType() == PatternType::PATTERN_TYPE_COPY)
        { // Set the value as is
            $cell->setValue($ptype->currentValue);
        }
    }

    private function buildTranInfo(Worksheet $worksheet, int $eachRowMarker): array
    {
        // The fields for binding each would always be listed in the next row
        $row = $eachRowMarker + 1;
        $hCol = Coordinate::columnIndexFromString($worksheet->getHighestColumn());
        // Create prop range
        $propRange = [];
        for($cc = 1; $cc <= $hCol; $cc++)
        {
            $cCell = $worksheet->getCellByColumnAndRow($cc, $row);


            $ptype = $this->parsePattern($worksheet, $cCell);
            if($ptype->getType() == PatternType::PATTERN_TYPE_NONE)
            {
                // Since this is an Array, we set the cell value to be copied
                $ptype->setType(PatternType::PATTERN_TYPE_COPY);
            }

            $propRange[$cc] = [
                'type' => $ptype,
                'range' => $cCell->getMergeRange(),
            ];
        }
        return $propRange;
    }

}
