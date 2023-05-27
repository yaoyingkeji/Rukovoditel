<?php

/**
 * @link http://www.vishwayon.com/
 * @copyright Copyright (c) 2020 Vishwayon Software Pvt Ltd
 * @license MIT
 */

require '../vendor/autoload.php';

/**
 * StepService allows publishing PhpStep as a service inside a web-server.
 * Publish example: http://localhost/php-step/StepService.php
 * 
 * 
 * With this service, you can create an independent server outside your current php process
 * Service Rules:
 * 1) Request should always be of type POST
 * 2) Following parameters should be available in request body:
 *      a) data:        The json encoded model
 *      b) inputFile:   The template file name with absolute path
 *      c) outputFile:  The file name with absolute path where the file will be rendered. Ensure that this path is writable by the process
 * 
 * @author girish
 */
if (filter_input(INPUT_SERVER, "REQUEST_METHOD") == "POST") {
    // Get the model
    $model = json_decode(filter_input(INPUT_POST, "data"));
    // Get the xslx filename
    $sfile = filter_input(INPUT_POST, 'inputFile');
    $tfile = filter_input(INPUT_POST, 'outputFile');
    // Start the Reader
    $reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
    $ss = $reader->load($sfile);
    $worksheet = $ss->getActiveSheet();

    $re = new PhpStep\RenderWorksheet();
    $re->applyData($worksheet, $model);

    $writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx');
    $writer->save($tfile);
}