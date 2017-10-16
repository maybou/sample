<?php
require '../vendor/autoload.php';

$dir = "images/";

//trim characters other than dates
while( $file_name = readdir($dir) ){
    if(preg_match('/\.png$/',$file_name)){
        rename("test/".$file_name , "test/".str_replace("スクリーンショット ","",$file_name));
    }
}

if (is_dir($dir) && $handle = opendir($dir)) {
    while (($file = readdir($handle)) !== false) {
        if (filetype($path = $dir . $file) == "file") {

            //Get date from filename
            $date = str_replace(".png", "",$file);
            $date = str_replace(".", ":",$date);


            $str =  (new TesseractOCR($dir .$file))
                ->run();

            $arrayStr = [];
            $arrayStr = explode(" ", $str);


            if(strlen( $arrayStr[6]) === 38 || strlen( $arrayStr[6]) === 39){
                 //get the wpm from the result
                $cut = 4;
                $s = substr( $arrayStr[6] , 0 , strlen($arrayStr[6])-$cut );
                $s = substr($s,-4);

                $data[] = [
                    "date" => $date,
                    "wpm" => $s
                ];

            }
        }
    }
}

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jquery.jqplot.min.js"></script>
    <script language="javascript" type="text/javascript" src="js/jqplot.dateAxisRenderer.min.js"></script>
    <link rel="stylesheet" type="text/css" href="css/jquery.jqplot.min.css" />
</head>
<body>
<div id="jqPlot-sample" style="height: 600px; width: 1400px;"></div>
<script>
    jQuery( function() {
        jQuery . jqplot(
            'jqPlot-sample',
            [
                [
                    <?php
                    $last_i  = key(array_slice($data, -1, 1, true));
                    foreach($data as $i => $value){
                        echo '["'.$value['date'].'",'.$value['wpm'].']';
                        if ($i !== $last_i) {
                            echo ',';
                        }
                    }
                    ?>
                ]
            ],

            {
                axes:{
                    xaxis:{
                        renderer: jQuery . jqplot . DateAxisRenderer,
                        min: '2017-08-21 00:00:00',
                        max: '2017-10-17 00:00:00',
                        tickInterval: '2 day',
                        tickOptions:{formatString:'%m/%d'}


                    }
                },
                seriesDefaults: {
                    color: '#000000',
                    showLine: false,
                    markerOptions: {
                        size: 16
                    },
                    pointLabels: {
                        show: true,
                        location: 'n',
                        ypadding: -12,
                    }
                }
            }
        );
    } );
</script>
</body>