<?php

$data = array(
//zero value
		'nul' => 'ноль',
//form 1-9
		'ten' => 
			array(
					array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
					array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
			),		
//from 10-19
		'a20' => 
			array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать'),
//from 20-90
		'tens' => 
			array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто'),
//from 100-900
		'hundred' => 
			array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот'),
//units
		'unit' => 
			array( // Units
				array('копейка' ,'копейки' ,'копеек',	 1),
				array('рубль'   ,'рубля'   ,'рублей'    ,0),
				array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
				array('миллион' ,'миллиона','миллионов' ,0),
				array('миллиард','миллиарда','миллиардов',0),
			)	
);