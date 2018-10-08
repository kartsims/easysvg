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
        $text = "Проверка текста\netc.";

        $svg = new EasySVG();
        $svg->setFontSVG("Lobster-Regular-Cyrillic.svg");
        $svg->setFontSize(80);
        $svg->setFontColor('#000000');
        $svg->setLineHeight(1.2);
        $svg->setLetterSpacing(.1);
        $svg->addText($text);
        // set width/height according to text
        list($textWidth, $textHeight) = $svg->textDimensions($text);
        $svg->addAttribute("width", $textWidth."px");
        $svg->addAttribute("height", $textHeight."px");
        echo $svg->asXML();
        ?>
        <br/><br/><br/>
        <h2>PHP code</h2>
        <pre>
                $text = "Simple text display\netc.";

                $svg = new EasySVG();
                $svg->setFontSVG("om_telolet_om-webfont.svg");
                $svg->setFontSize(80);
                $svg->setFontColor('#000000');
                $svg->setLineHeight(1.2);
                $svg->setLetterSpacing(.1);
                $svg->addText($text);
                // set width/height according to text
                list($textWidth, $textHeight) = $svg->textDimensions($text);
                $svg->addAttribute("width", $textWidth."px");
                $svg->addAttribute("height", $textHeight."px");
                echo $svg->asXML();
            </pre>
        <br/><br/><br/>

        <h1>Centered text</h1>
        <?php
        $text = "Simple text";

        $svg = new EasySVG();
        $svg->addAttribute("width", "600px");
        $svg->addAttribute("height", "200px");
        $svg->addAttribute("style", "border: dashed 1px #aaa");
        $svg->setFontSVG("om_telolet_om-webfont.svg");
        $svg->setFontSize(80);
        $svg->setFontColor('#000000');
        $svg->setLetterSpacing(.1);
        $svg->addText($text, "center", "center");
        echo $svg->asXML();
        ?>
        <br/><br/><br/>
        <h2>PHP code</h2>
        <pre>
                $text = "Simple text display\netc.";

                $svg = new EasySVG();
                $svg->addAttribute("width", "600px");
                $svg->addAttribute("height", "200px");
                $svg->addAttribute("style", "border: dashed 1px #aaa");
                $svg->setFontSVG("om_telolet_om-webfont.svg");
                $svg->setFontSize(80);
                $svg->setFontColor('#000000');
                $svg->setLetterSpacing(.1);
                $svg->addText($text, "center", "center");
                echo $svg->asXML();
            </pre>
        <br/><br/><br/>
    </div>
</div>

</body>
</html>
