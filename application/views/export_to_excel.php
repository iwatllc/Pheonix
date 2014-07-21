<?php
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

if (PHP_SAPI == 'cli')
	die('This example should only be run from a Web Browser');

function num2alpha($n)
{
    for($r = ""; $n >= 0; $n = intval($n / 26) - 1)
        $r = chr($n%26 + 0x41) . $r;
    return $r;
}


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
// Set document properties
$objPHPExcel->getProperties()->setCreator("Report Manager")
							 ->setLastModifiedBy("Report Manager")
							 ->setTitle("Report Manager: Resulted Report")
							 ->setSubject("Report Manager: Resulted Report")
							 ->setDescription("Member Report, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Report Manager");

//Excel column name
$objPHPExcel->setActiveSheetIndex(0);
foreach($results AS $value)
{
	$i = 0;
	foreach($value as $k=>$v){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(num2alpha($i)."1", $k);
		$objPHPExcel->getActiveSheet()->getStyle(num2alpha($i)."1".":".num2alpha($i)."1")->getFont()->setBold(true);
		$i++;
	}
	break;
}

//Excel column values
$j=2;
$objPHPExcel->setActiveSheetIndex(0);
foreach($results AS $value)
{
	$i = 0;
	foreach($value as $k=>$v){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(num2alpha($i).$j, $v);
		$i++;
	}
	$j++;
	$objPHPExcel->setActiveSheetIndex(0);
	
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

$filename = 'Report Manager';
$filename .= ' '.date("d M Y").'.xls';

// Redirect output to a client’s web browser (Excel5)
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$filename.'"');
header('Cache-Control: max-age=0');
//If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

//If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save('php://output');
exit;
