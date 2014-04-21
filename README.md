# EasySVG for PHP

Generate SVG images from SVG font easily.

The SVG data produced here is directly extracted from the font .svg file. This **does not use the &lt;text&gt; tag**.

## Usage

	require 'easySVG.php';
	$svg = new EasySVG();
	$svg->setFont("paris-bold-webfont.svg", 100, "#000000");
	$svg->addText("Simple text display");
	$svg->addAttribute("width", "800px");
	$svg->addAttribute("height", "100px");
	echo $svg->asXML();

This will output inline SVG for you to play with. You can **echo** it, **save** it to a file or whatever.

## Method reference

#### setFont($path, $size, $color)

Sets the font attributes. This is a shortcut for :

	$this->setFontSVG($path);
	$this->setFontSize($size);
	$this->setFontColor($color);

These 3 methods are explicit enough, I won't go through these in here.

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

Any questions welcome : kartsims -@- gmail
