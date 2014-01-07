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
            
            <h2>Text example</h2>
            <?php
            $svg = new EasySVG();
            $svg->setFontSVG("paris-bold-webfont.svg");
            $svg->setFontSize(100);
            $svg->setFontColor('#000000');
            $svg->addText("Simple text display");
            $svg->addAttribute("width", "800px");
            $svg->addAttribute("height", "100px");
            echo $svg->asXML();
            ?>
            <pre>
                $svg = new EasySVG();
                $svg->setFontSVG("paris-bold-webfont.svg");
                $svg->setFontSize(100);
                $svg->setFontColor('#000000');
                $svg->addText("Simple text display");
                $svg->addAttribute("width", "800px");
                $svg->addAttribute("height", "300px");
                echo $svg->asXML();</pre>
            <hr>
            <textarea style="width:100%;height:200px;"><?php echo $svg->asXML(); ?></textarea>
            
        </div>
    </div>

</body>
</html>
