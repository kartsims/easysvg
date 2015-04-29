<?php
// EasySVG demo page
require '../src/EasySVG.php'; 
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
</head>
<body>
    <div class="row">
        <div class="col-xs-12">

            <h1>EasySVG demo</h1>
            
            <?php
            $text = "Simple text display\netc.";

            $svg = new EasySVG();
            $svg->setFontSVG("paris-bold-webfont.svg");
            $svg->setFontSize(100);
            $svg->setFontColor('#000000');
            $svg->setLineHeight(1.2);
            $svg->addText($text);
            // set width/height according to text
            list($textWidth, $textHeight) = $svg->textDimensions($text);
            $svg->addAttribute("width", $textWidth."px");
            $svg->addAttribute("height", $textHeight."px");
            echo $svg->asXML();
            ?>
            <br/><br/><br/>
            <pre>
    $text = "Simple text display\netc.";

    $svg = new EasySVG();
    $svg->setFontSVG("paris-bold-webfont.svg");
    $svg->setFontSize(100);
    $svg->setFontColor('#000000');
    $svg->setLineHeight(1.2);
    $svg->addText($text);
    // set width/height according to text
    list($textWidth, $textHeight) = $svg->textDimensions($text);
    $svg->addAttribute("width", $textWidth."px");
    $svg->addAttribute("height", $textHeight."px");
    echo $svg->asXML();</pre>
            <br/><br/><br/>
            <textarea style="width:100%;height:500px;"><?php echo $svg->asXML(); ?></textarea>
            
        </div>
    </div>

</body>
</html>
