# EasySVG for PHP

Generate SVG images from SVG font easily.

The SVG data produced here is directly extracted from the font .svg file. This **does not use the &lt;text&gt; tag**.

## Simple usage

    require 'easySVG.php';

    $svg = new EasySVG();
    $svg->setFont("paris-bold-webfont.svg", 100, '#000000');
    $svg->addText("Simple text display");
    $svg->addAttribute("width", "800px");
    $svg->addAttribute("height", "120px");
    echo $svg->asXML();

## Advanced usage

    require 'easySVG.php';

    $text = "Simple text display\netc.";

    $svg = new EasySVG();
    $svg->setFontSVG("paris-bold-webfont.svg");
    $svg->setFontSize(100);
    $svg->setFontColor('#000000');
    $svg->setLineHeight(1.2);
    $svg->setLetterSpacing(.1);
    $svg->addText($text);
    // set width/height according to text
    list($textWidth, $textHeight) = $svg->textDimensions($text);
    $svg->addAttribute("width", $textWidth."px");
    $svg->addAttribute("height", $textHeight."px");
    echo $svg->asXML();

This will output inline SVG for you to play with. You can **echo** it, **save** it to a file or whatever.

## Method reference

#### setFont($path, $size, $color)

Sets the font attributes. This is a shortcut for :

    $this->setFontSVG($path);
    $this->setFontSize($size);
    $this->setFontColor($color);

These 3 methods are explicit enough, I won't go through these in here.

#### setLineHeight($value)

Adds a CSS-like line-height value. A numeric value (float) where 1 is the line height defined by the font itself.

#### setLetterSpacing($value)

Adds a CSS-like letter-spacing value. A numeric value (float) expressed in em where 1 is the width of the `m` character.

#### addText($text, $x, $y, $attributes=array())

Add text to the SVG (will be converted to simple path)

- $text : String UTF-8 encoded
- $x : X position of the text (starting from left)
- $y : Y position of the text (starting from top)
- $attributes (optional) : list of tag attributes

#### asXML()

Return XML string of the whole SVG.

#### addAttribute($key, $value)

Add an attribute to the main SVG.

### SVG data manipulation methods

You may need these to play around with SVG definitions.

#### defTranslate($def, $x=0, $y=0)

Applies a translate transformation to a definition. This basically applies matrix calculation to a definition.

#### defRotate($def, $angle, $x=0, $y=0)

Applies a translate transformation to definition. This basically applies matrix calculation to a definition.

#### defScale($def, $x=1, $y=1)

Applies a scale transformation to definition. This basically applies matrix calculation to a definition.

#### textDef($text)

Returns a SVG-formatted definition of a string. This method is used by addText method.

- $text : String UTF-8 encoded

#### textDimensions($text)

Returns the width and height of a string. This method is also used to set the width/height of the SVG (if none specified).

- $text : String UTF-8 encoded

#### unicodeDef($code)

Returns a SVG-formatted definition of an unicode character.

- $code : Unicode definition (in hex format)

#### characterWidth($char, $is_unicode=false)

Returns the width of a character.

- $char : Character
- $is_unicode : Boolean that tells if the character is a unicode string or a UTF-8 character.

#### addPath($def, $attributes=array())

Add a path to the SVG data

- $def : SVG definition
- $attributes (optional) : list of tag attributes

### Utility methods

#### clearSVG()

Resets the SVG data. Used to start a new SVG without creating a new instance.

#### defApplyMatrix($def, $matrix)

Apply a matrix to a definition. Used to apply any kind of transformations, you shouldn't need this, but it is available so you may play with it.

## License

MIT. Please feel free to pull, fork, and so on.
