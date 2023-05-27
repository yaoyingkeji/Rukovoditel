# PhpStep

PHP Spreadsheet Template Engine 

Thanks to [PHPOffice/PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet), we are able to generate various types of spreadsheets directly from php.
PhpStep is an attempt to automate data populated spreadsheets using **template tags**.

In this project, we put together an existing xslx file containing some template attributes and a structured model 
or json data to output a ready to use spreadsheet with user readable data.

### Dependencies

* [PHPOffice/PhpSpreadsheet](https://github.com/PHPOffice/PhpSpreadsheet)
* PHP 7.2

### Installation and Usage

```bash
composer require vishwayon/phpstep "^0.1"
```

Or, use the git repository.

## Supported Template Tags

*   **$F{field_name}** - A field/property in the class/data source
*   **$Each{Iterator}** - Any collection object/array implementing Iterator interface 

### Sample Code

First, create an sample.xlsx file with following structure:

|      A        |       B       |
| ------------- | ------------- |
| $F{message}   |               |
|               |               |
| Country       | Population    |
| $Each{stats}  |               |
| $F{country}   |$F{population} |


You can apply various formats to the cells and also create normal formulas.

```php

require '../vendor/autoload.php';

$model = new \stdClass();
$model->message = 'Hello World!';
$model->stats = [
    ['country' => 'India', 'population' => 1300]
    ['country' => 'USA', 'population' => 330,
    ['country' => 'Russia', 'population' => 145
];

$reader = PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
$ss = $reader->load("sample.xlsx");
$worksheet = $ss->getActiveSheet();

$re = new PhpStep\RenderWorksheet();
$re->applyData($worksheet, $model);

$writer = PhpOffice\PhpSpreadsheet\IOFactory::createWriter($ss, 'Xlsx');
$writer->save('sampleResult.xlsx');

```
For complex methods, refer to test/TestRenderWorksheet.php and testData.xlsx

## Limitations
* Absolute formulas in the worksheet (e.g: $D$13) will not work. They would reference incorrect cells after rendering

### License
PhpStep is licensed under [MIT](https://github.com/vishwayon/PhpStep/blob/master/LICENSE).
