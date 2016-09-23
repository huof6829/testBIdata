<?php


require_once('jpgraph/jpgraph.php');
require_once('jpgraph/jpgraph_bar.php');
require_once('jpgraph/jpgraph_line.php');


error_reporting(E_ALL);


// PHPExcel_IOFactory 
require_once './phpexcel/PHPExcel/IOFactory.php';


// Check prerequisites
if (!file_exists("2.xlsx")) {
    exit("not found 2.xlsx.\n");
}

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_phpTemp;
$cacheSettings = array( ' memoryCacheSize ' => '16MB');
PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

// filter
class MyReadFilter implements PHPExcel_Reader_IReadFilter
{
    public function readCell($column, $row, $worksheetName = '') {
        if ( $column <= "G" ) {
            return true;
        }

        return false;
    }
}


//设置Excel格式
$reader = PHPExcel_IOFactory::createReader('Excel2007'); 
$reader->setReadDataOnly(true);
//$reader->setLoadSheetsOnly( array("Worksheet1", "Worksheet2") );
$reader->setReadFilter( new MyReadFilter() );
// 载入excel文件
$PHPExcel = $reader->load("2.xlsx"); 
// 读取工作表
$sheet = $PHPExcel->getSheet(0); 
// 取得总行数
$highestRow = $sheet->getHighestRow(); 
// 取得总列数
$highestColumm = $sheet->getHighestColumn(); 

#echo $highestRow;
#echo '<br>';
#echo $highestColumm;
#echo '<br>';

// 循环读取每个单元格的数据 
$data = array();
$_column = $_GET["column"];
//$highestRow = 10;

if ($_column == 'B' || $_column == 'G'){

    for ($row = 2; $row <= $highestRow; $row++){
        $data[$row-2] = strval($sheet->getCell($_column.$row)->getValue());
    }
}
else {
    //行数是以第1行开始        //列数是以A列开始
    for ($row = 2; $row <= $highestRow; $row++){
        $data[$row-2] = intval($sheet->getCell($_column.$row)->getValue());
    }
}
//$data = array(1792,1792,1792,1792,1792,1792,1792,1792,1792,1792);
//print_r($data);
//var_dump($data);
//echo "<br />";
//echo count($data)."<br />";

$data_t = array_count_values($data);
//var_dump($data_x);
//echo "<br />";
//echo count($data_x)."<br />";
 

//// testdata
//$data=array(31,44,49,40,24,47,12);
//$data=array('1566'=>1,'0'=>2,'35'=>5);
$data_x=array();
$data_y=array();
foreach ($data_t as $key => $value ) {
    $data_x[]=$key;
    $data_y[]=$value;
}

$title="";
$xName="";
switch($_column) {
case 'F':
    $title = "goup id static";
    $xName = "goup id";
    break;
case 'E':
    $title = "assignee id static";
    $xName = "assignee id";
    break;
case 'D':
    $title = "requester id static";
    $xName = "requester id";
    break;
case 'G':
    $title = "source static";
    $xName = "source";
    break;
case 'B':
    $title = "status static";
    $xName = "status";
    break;
default:
    $title = "no data static";
    $xName = "no id";
    break;
}    


BarSet($title,$xName,"tota number",$data_x,$data_y);


function BarSet($title,$xName,$yName,$xValue,$yValue,$w=700,$h=300) {

    //设置图形容器
    $graph=new Graph($w,$h);
    $graph->img->SetMargin(60,30,30,40);
    $graph->SetScale("textlin");
    // $graph->SetMarginColor("teal");
    // $graph->SetShadow();


    //建立一个柱形
    $bplot=new BarPlot($yValue);
    $bplot->SetWidth(0.6);

    //设置渐变填充的颜色
    $tcol=array(100,100,255);
    $fcol=array(255,100,100);
    $bplot->SetFillGradient($fcol,$tcol,GRAD_VERT);
    $bplot->SetFillColor("orange");
    $graph->Add($bplot);

    //设置图形标题
    $graph->title->Set($title);
    $graph->title->SetColor("red");
    $graph->title->SetFont(FF_FONT1,FS_BOLD,12);

    //设置坐标和标签
    $graph->xaxis->SetColor("black","red");
    $graph->yaxis->SetColor("black","red");

    //设置坐标字体
    $graph->xaxis->SetFont(FF_FONT1,FS_NORMAL,10);
    $graph->yaxis->SetFont(FF_FONT1,FS_NORMAL,10);
    $graph->yaxis->title->Set($yName);
    $graph->yaxis->title->SetColor("black");
    $graph->yaxis->title->SetFont(FF_FONT1,FS_NORMAL,10);

    //设置x坐标的标题(颜色和字体)
    $graph->xaxis->title->Set($xName);
    $graph->xaxis->title->SetColor("black");
    $graph->xaxis->title->SetFont(FF_FONT1,FS_NORMAL,10);

    $graph->xaxis->SetTickLabels($xValue);

    //发送到浏览器
    $graph->Stroke();

}



?>






