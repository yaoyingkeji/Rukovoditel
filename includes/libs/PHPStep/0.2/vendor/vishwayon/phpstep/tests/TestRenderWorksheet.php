<?php

/**
 * @link http://www.vishwayon.com/
 * @copyright Copyright (c) 2020 Vishwayon Software Pvt Ltd
 * @license MIT
 */
require '../vendor/autoload.php';

$model = new \stdClass();
$model->branch_name = 'First Company';
$model->period_desc = 'Report Period: Between 2020-04-01 And 2020-04-30';
$model->prod_qty = 7305.900;
$model->prm_rm_val = 206510.12;
$model->prm_ltr_rate = 28.266;
$model->prm_cost_to_ltr = .2372; 
$model->prm_stock_tran = [
    ['material_name' => 'Skimmed Milk Powder(SMP)', 'qty' => 504, 'rate' => 316.774, 'amt' => 159653.84, 'pcnt' => .5813],
    ['material_name' => 'Cream', 'qty' => 26,177, 'rate' => 188.755, 'amt' => 4941035, 'pcnt' => .208],
    ['material_name' => 'Sugar', 'qty' => 104785, 'rate' => 33.022, 'amt' => 3460238, 'pcnt' => .146],
    ['material_name' => 'Raw Chilled Milk', 'qty' => 82900, 'rate' => 27.739, 'amt' => 2299535, 'pcnt' => .097],
    ['material_name' => 'Refined Palm Kernel Oil', 'qty' => 22041, 'rate' => 99.100, 'amt' => 2184272, 'pcnt' => .092],
    ['material_name' => 'Liquid Glucose', 'qty' => 41195, 'rate' => 34.006, 'amt' => 1400893, 'pcnt' => .059]
];
$model->orm_rm_val = 31278.26;
$model->orm_ltr_rate = 4.281;
$model->orm_cost_to_ltr = .0359; 
$model->orm_stock_tran = [
    ['material_name' => 'Cocoa Powder JB100', 'qty' => 2842, 'rate' => 201.821, 'amt' => 573,489, 'pcnt' => .024],
    ['material_name' => 'Cocoa Powder JB800 LA', 'qty' => 1863, 'rate' => 201.288, 'amt' => 375012, 'pcnt' => .016],
    ['material_name' => 'Premix 107', 'qty' => 1386, 'rate' => 246.002, 'amt' => 340958, 'pcnt' => .014],
    ['material_name' => 'Premix 007', 'qty' => 900, 'rate' => 245.007, 'amt' => 220,506, 'pcnt' => .009],
    ['material_name' => 'Premix 307', 'qty' => 278, 'rate' => 442.055, 'amt' => 122,670, 'pcnt' => .005],
    ['material_name' => 'Premix 707', 'qty' => 61, 'rate' => 1,017.766, 'amt' => 61575, 'pcnt' => .003],
    ['material_name' => 'Premix 407', 'qty' => 4, 'rate' => 781.368, 'amt' => 3461, 'pcnt' => .000]
];
$model->pm_rm_val = 103872.80;
$model->pm_ltr_rate = 14.218;
$model->pm_cost_to_ltr = .1193; 
$model->pm_stock_tran = [
    ['material_name' => 'Packing Roll - Grape Candy', 'qty' => 85, 'rate' => 250.682, 'amt' => 21307.94, 'pcnt' => .2051],
    ['material_name' => 'Packing Roll Fun Bites Chocolate 15 Ml', 'qty' => 65, 'rate' => 250.866, 'amt' => 16306.32, 'pcnt' => .1570],
    ['material_name' => 'Wooden Stick 114 mm', 'qty' => 54200, 'rate' => 0.217, 'amt' => 11772.24, 'pcnt' => .1133],
    ['material_name' => 'Corrugated Box', 'qty' => 2021, 'rate' => 5.248, 'amt' => 10605.40, 'pcnt' => .1021],
    ['material_name' => 'Wooden Stick 65 mm', 'qty' => 59500, 'rate' => 0.151, 'amt' => 8990.45, 'pcnt' => .0866]
];
$model->st_qty = 26847.755;
$model->st_amt = 3184639.69	;
$model->st_rate = 118.618;
$model->sale_qty = 57707.220;
$model->sale_amt = 6892685.93;
$model->sale_rate = 119.442;

$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
//$reader->setReadDataOnly(TRUE);
$ss = $reader->load("testData.xlsx");
$worksheet = $ss->getActiveSheet();

$re = new PhpStep\RenderWorksheet();
$re->applyData($worksheet, $model);

$writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx');
$writer->save('result.xlsx');