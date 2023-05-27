<?php


FIX 1: //utf8_encode is deprecated in 8.2
//includes\libs\PHPWord\1.0.0\vendor\phpoffice\phpword\src\PhpWord\TemplateProcessor.php line 256
				$subject = utf8_encode($subject); //deprecated in 8.2

//replaced to 
				$subject = mb_convert_encoding($subject, 'UTF-8', mb_list_encodings());  

FIX 2: //fix text-break to export in html
//includes\libs\PHPWord\1.0.0\vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Style\Font.php    

				/*
         * custom code to add page break, using font-name as text-break-1 value
         * in file vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Element\Text.php   
         * private function getFontStyle():     
         */
        if(isset($font) and preg_match('/text-break-([\d]+)/',$font,$matches))
        {            
            $css['text-break'] = $matches[1];
        }       
				
//includes\libs\PHPWord\1.0.0\vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Element\Text.php				
				/*
         * custom code to handle text-break
         * option added in vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Style\Font.php
         * public function write()
         */
        if(preg_match('/text-break: ([\d]+)/',$style,$matches))       
        {                   
            $text_break = ($matches[1]>0 ? $matches[1] : 1);
            $this->closingTags .= str_repeat('<br>', $text_break);
        }
				
FIX 3: //remov padding and margin in <p> tag
//includes\libs\PHPWord\1.0.0\vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Element\TextBreak.php				
				$content = '<p style="margin-top: 0; margin-bottom: 0;">&nbsp;</p>' . PHP_EOL;
				
FIX 4: //fixed table border in html templated, added border-collapse
//includes\libs\PHPWord\1.0.0\vendor\phpoffice\phpword\src\PhpWord\Writer\HTML\Part\Head.php

						'table' => [
                //'border' => '1px solid black',
                //'border-spacing' => '0px',
                'width ' => '100%',
                'border-collapse' => 'collapse',
            ],
            'td' => [
                'border' => '1px solid black',
                'padding' => '3px 5px 3px 5px',
            ],
				