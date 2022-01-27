<?php

declare(strict_types=1);

/**
 * EasySVG - Generate SVG from PHP
 * @author Simon Tarchichi <kartsims@gmail.com>
 * @version 0.1b
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute/transform
 * @see http://stackoverflow.com/questions/14684846/flattening-svg-matrix-transforms-in-inkscape
 * @see http://stackoverflow.com/questions/7742148/how-to-convert-text-to-svg-paths
 */
class EasySVG
{
    protected stdClass $font;
    protected SimpleXMLElement $svg;

    public function __construct()
    {
        // default font data
        $this->font = new stdClass();
        $this->font->id = '';
        $this->font->horizAdvX = 0;
        $this->font->unitsPerEm = 0;
        $this->font->ascent = 0;
        $this->font->descent = 0;
        $this->font->glyphs = [];
        $this->font->hkern = [];
        $this->font->useKerning = false;
        $this->font->size = 20;
        $this->font->color = null;
        $this->font->lineHeight = 1;
        $this->font->letterSpacing = 0;

        $this->clearSVG();
    }

    public function clearSVG(): void
    {
        $this->svg = new SimpleXMLElement('<svg></svg>');
        $this->svg->addAttribute('version', '1.1');
        $this->svg->addAttribute('xmlns', 'http://www.w3.org/2000/svg');
    }

    /**
     * Function takes UTF-8 encoded string and returns unicode number for every character.
     * @param string $str
     * @return array
     */
    private function _utf8ToUnicode(string $str): array
    {
        $unicode = [];
        $values = [];
        $lookingFor = 1;

        for ($i = 0, $iMax = strlen($str); $i < $iMax; $i++) {
            $thisValue = ord($str[ $i ]);
            if ($thisValue < 128) {
                $unicode[] = $thisValue;
            } else {
                if (count($values) === 0) {
                    $lookingFor = ($thisValue < 224) ? 2 : 3;
                }
                $values[] = $thisValue;
                if (count($values) === $lookingFor) {
                    $number = ($lookingFor === 3) ?
                        (($values[0] % 16) * 4096) + (($values[1] % 64) * 64) + ($values[2] % 64) :
                        (($values[0] % 32) * 64) + ($values[1] % 64);

                    $unicode[] = $number;
                    $values = [];
                    $lookingFor = 1;
                }
            }
        }

        return $unicode;
    }

    /**
     * Set font params (short-hand method)
     * @param string $filepath
     * @param integer $size
     * @param string|null $color
     */
    public function setFont(string $filepath, int $size, string $color = null): void
    {
        $this->setFontSVG($filepath);
        $this->setFontSize($size);
        if ($color) {
            $this->setFontColor($color);
        }
    }

    /**
     * Set font size for display
     * @param int $size
     * @return void
     */
    public function setFontSize(int $size): void
    {
        $this->font->size = $size;
    }

    /**
     * Set kerning support flag
     * @param bool $bool
     * @return void
     */
    public function setUseKerning(bool $bool): void
    {
        $this->font->useKerning = $bool;
    }

    /**
     * Set font color
     * @param string $color
     * @return void
     */
    public function setFontColor(string $color): void
    {
        $this->font->color = $color;
    }

    /**
     * Set the line height from default (1) to custom value
     * @param float $value
     * @return void
     */
    public function setLineHeight(float $value): void
    {
        $this->font->lineHeight = $value;
    }

    /**
     * Set the letter spacing from default (0) to custom value
     * @param float $value
     * @return void
     */
    public function setLetterSpacing(float $value): void
    {
        $this->font->letterSpacing = $value;
    }

    /**
     * Function takes path to SVG font (local path) and processes its xml
     * to get path representation of every character and additional
     * font parameters
     * @param string $filepath
     * @return void
     */
    public function setFontSVG(string $filepath): void
    {
        $this->font->glyphs = [];
        $z = new XMLReader();
        $z->open($filepath);

        // move to the first <product /> node
        while ($z->read()) {
            $name = $z->name;

            if ($z->nodeType === XMLReader::ELEMENT) {
                if ($name === 'font') {
                    $this->font->id = $z->getAttribute('id');
                    $this->font->horizAdvX = $z->getAttribute('horiz-adv-x');
                }

                if ($name === 'font-face') {
                    $this->font->unitsPerEm = $z->getAttribute('units-per-em');
                    $this->font->ascent = $z->getAttribute('ascent');
                    $this->font->descent = $z->getAttribute('descent');
                }

                if ($name === 'glyph') {
                    $unicode = $z->getAttribute('unicode');
                    $unicode = $this->_utf8ToUnicode($unicode);

                    if (isset($unicode[0])) {
                        $unicode = $unicode[0];

                        $this->font->glyphs[$unicode] = new stdClass();
                        $this->font->glyphs[$unicode]->horizAdvX = $z->getAttribute('horiz-adv-x');
                        if (empty($this->font->glyphs[$unicode]->horizAdvX)) {
                            $this->font->glyphs[$unicode]->horizAdvX = $this->font->horizAdvX;
                        }
                        $this->font->glyphs[$unicode]->d = $z->getAttribute('d');

                        // save em value for letter spacing (109 is unicode for the letter 'm')
                        if ($unicode === '109') {
                            $this->font->em = $this->font->glyphs[$unicode]->horizAdvX;
                        }
                    }
                }

                if ($name === 'hkern') {
                    $u1 = $this->_utf8ToUnicode($z->getAttribute('u1'));
                    $u2 = $this->_utf8ToUnicode($z->getAttribute('u2'));
                    if (isset($u1[0], $u2[0])) {
                        $k = $z->getAttribute('k');
                        $this->font->hkern[$u1[0]][$u2[0]] = $k;
                    }
                }
            }
        }
    }

    /**
     * Add a path to the SVG
     * @param string $def
     * @param array $attributes
     * @return null|SimpleXMLElement
     */
    public function addPath(string $def, array $attributes=[]): ?SimpleXMLElement
    {
        $path = $this->svg->addChild('path');
        if ($path === null) {
            return null;
        }

        foreach ($attributes as $key => $value) {
            $path->addAttribute($key, $value);
        }

        $path->addAttribute('d', $def);

        return $path;
    }

    /**
     * Add a text to the SVG
     * @param string $text
     * @param float|string $x
     * @param float|string $y
     * @param array $attributes
     * @return null|SimpleXMLElement
     */
    public function addText(string $text, $x = 0, $y = 0, array $attributes=[]): ?SimpleXMLElement
    {
        $def = $this->textDef($text);

        if ($x === 'center' || $y === 'center') {
            [$textWidth, $textHeight] = $this->textDimensions($text);
        }

        // center horizontally
        if ($x === 'center') {
            if ($this->svg['width'] === null) {
                throw new Error('SVG width has to be set to center the text horizontally');
            }
            $x = ((int)$this->svg['width'] - $textWidth) / 2;
        }

        // center vertically
        if ($y === 'center') {
            if ($this->svg['height'] === null) {
                throw new Error('SVG height has to be set to center the text vertically');
            }
            $y = ((int)$this->svg['height'] - $textHeight) / 2;
        }

        if ($x !== 0 || $y !== 0) {
            $def = $this->defTranslate($def, $x, $y);
        }

        if ($this->font->color) {
            $attributes['fill'] = $this->font->color;
        }

        return $this->addPath($def, $attributes);
    }

    /**
     * Function takes UTF-8 encoded string and size, returns xml for SVG paths representing this string.
     * @param string $text UTF-8 encoded text
     * @return string xml for text converted into SVG paths
     */
    public function textDef(string $text): string
    {
        $def = [];

        $horizAdvX = 0;
        $horizAdvY = $this->font->ascent + $this->font->descent;
        $fontSize = (float)$this->font->size / $this->font->unitsPerEm;
        $textUnicode = $this->_utf8ToUnicode($text);

        $prevLetter = '';

        foreach ($textUnicode as $letter) {
            // kern
            if ($this->font->useKerning && isset($this->font->hkern[$prevLetter][$letter])) {
                $horizAdvX -= $this->font->hkern[$prevLetter][$letter] * $fontSize;
            }

            //ignore this glyph instead of throwing an error if the font does not define it
            if (!array_key_exists($letter, $this->font->glyphs)) {
                continue;
            }

            // line break support (10 is unicode for linebreak)
            if ($letter === 10) {
                $horizAdvX = 0;
                $horizAdvY += $this->font->lineHeight * ($this->font->ascent + $this->font->descent);
                continue;
            }

            // extract character definition
            $d = $this->font->glyphs[$letter]->d;

            // transform typo from original SVG format to straight display
            $d = $this->defScale($d, $fontSize, -$fontSize);
            $d = $this->defTranslate($d, $horizAdvX, $horizAdvY*$fontSize*2);

            $def[] = $d;

            // next letter's position
            $horizAdvX += $this->font->glyphs[$letter]->horizAdvX * $fontSize + $this->font->em * $this->font->letterSpacing * $fontSize;

            $prevLetter = $letter;
        }
        return implode(' ', $def);
    }


    /**
     * Function takes UTF-8 encoded string and size, returns width and height of the whole text
     * @param string $text UTF-8 encoded text
     * @return array{width: float, height: float}
     */
    public function textDimensions(string $text): array
    {
        $fontSize = (float)$this->font->size / $this->font->unitsPerEm;
        $textUnicode = $this->_utf8ToUnicode($text);

        $lineWidth = 0;
        $lineHeight = ($this->font->ascent + $this->font->descent) * $fontSize * 2;

        $width = 0;
        $height = $lineHeight;

        $prevLetter = '';

        foreach ($textUnicode as $letter) {
            //ignore this glyph instead of throwing an error if the font does not define it
            if (!array_key_exists($letter, $this->font->glyphs)) {
                continue;
            }

            // line break support (10 is unicode for linebreak)
            if ($letter === 10) {
                $width = max($lineWidth, $width);
                $height += $lineHeight * $this->font->lineHeight;
                $lineWidth = 0;
                continue;
            }

            $lineWidth += $this->font->glyphs[$letter]->horizAdvX * $fontSize + $this->font->em * $this->font->letterSpacing * $fontSize;

            // kern
            if ($this->font->useKerning && isset($this->font->hkern[$prevLetter][$letter])) {
                $lineWidth -= $this->font->hkern[$prevLetter][$letter] * $fontSize;
            }

            $prevLetter = $letter;
        }

        // only keep the widest line's width
        $width = max($lineWidth, $width);

        return [$width, $height];
    }


    /**
     * Function takes unicode character and returns the UTF-8 equivalent
     * @param string $unicode
     * @return string
     */
    public function unicodeDef(string $unicode): string
    {
        $horizAdvY = $this->font->ascent + $this->font->descent;
        $fontSize =  (float)$this->font->size / $this->font->unitsPerEm;

        // extract character definition
        $d = $this->font->glyphs[hexdec($unicode)]->d;

        // transform typo from original SVG format to straight display
        $d = $this->defScale($d, $fontSize, -$fontSize);
        return $this->defTranslate($d, 0, $horizAdvY*$fontSize*2);
    }

    /**
     * Returns the character width, as set in the font file
     * @param string $char
     * @param boolean $is_unicode
     * @return float
     */
    public function characterWidth(string $char, bool $is_unicode = false)
    {
        if ($is_unicode) {
            $letter = hexdec($char);
        } else {
            $letter = $this->_utf8ToUnicode($char);
        }

        if (!isset($this->font->glyphs[$letter])) {
            return null;
        }

        $fontSize = (float)$this->font->size / $this->font->unitsPerEm;
        return $this->font->glyphs[$letter]->horizAdvX * $fontSize;
    }


    /**
     * Applies a translate transformation to definition
     * @param string $def definition
     * @param  float $x
     * @param  float $y
     * @return string
     */
    public function defTranslate(string $def, float $x = 0, float $y = 0): string
    {
        return $this->defApplyMatrix($def, [1, 0, 0, 1, $x, $y]);
    }

    /**
     * Applies a translate transformation to definition
     * @param string $def    Definition
     * @param float $angle  Rotation angle (degrees)
     * @param float $x      X coordinate of rotation center
     * @param float $y      Y coordinate of rotation center
     * @return string
     */
    public function defRotate(string $def, float $angle, float $x = 0, float $y = 0): string
    {
        if ($x === 0 && $y === 0) {
            $angle = deg2rad($angle);
            return $this->defApplyMatrix($def, [cos($angle), sin($angle), -sin($angle), cos($angle), 0, 0]);
        }

        // rotate by a given point
        $def = $this->defTranslate($def, $x, $y);
        $def = $this->defRotate($def, $angle);
        return $this->defTranslate($def, -$x, -$y);
    }

    /**
     * Applies a scale transformation to definition
     * @param string $def definition
     * @param float $x
     * @param float $y
     * @return string
     */
    public function defScale(string $def, float $x = 1, float $y = 1): string
    {
        return $this->defApplyMatrix($def, [$x, 0, 0, $y, 0, 0]);
    }

    /**
     * Calculates the new definition with the matrix applied
     * @param string $def
     * @param array $matrix
     * @return string
     */
    public function defApplyMatrix(string $def, array $matrix): string
    {
        // if there are several shapes in this definition, do the operation for each
        preg_match_all('/M[^zZ]*[zZ]/', $def, $shapes);
        $shapes = $shapes[0];
        if (count($shapes)>1) {
            foreach ($shapes as &$shape) {
                $shape = $this->defApplyMatrix($shape, $matrix);
            }
            return implode(' ', $shapes);
        }

        preg_match_all('/[a-zA-Z]+[^a-zA-Z]*/', $def, $instructions);
        $instructions = $instructions[0];

        $return = '';
        foreach ($instructions as &$instruction) {
            $i = preg_replace('/[^a-zA-Z]*/', '', $instruction);
            preg_match_all('/-?[0-9.]+/', $instruction, $coords);
            $coords = $coords[0];

            if (empty($coords)) {
                continue;
            }

            $new_coords = [];
            while (count($coords)>0) {

                // do the matrix calculation stuff
                [$a, $b, $c, $d, $e, $f] = $matrix;

                // exception for relative instruction
                if (preg_match('/[a-z]/', $i)) {
                    $e = 0;
                    $f = 0;
                }

                // convert horizontal lineto (relative)
                if ($i === 'h') {
                    $i = 'l';
                    $x = (float)array_shift($coords);
                    $y = 0;

                    // add new point's coordinates
                    $current_point = [
                        $a*$x + $c*$y + $e,
                        $b*$x + $d*$y + $f,
                    ];
                    $new_coords = array_merge($new_coords, $current_point);
                }

                // convert vertical lineto (relative)
                elseif ($i === 'v') {
                    $i = 'l';
                    $x = 0;
                    $y = (float)array_shift($coords);

                    // add new point's coordinates
                    $current_point = [
                        $a*$x + $c*$y + $e,
                        $b*$x + $d*$y + $f,
                    ];
                    $new_coords = array_merge($new_coords, $current_point);
                }

                // convert quadratic bezier curve (relative)
                elseif ($i === 'q') {
                    $x = (float)array_shift($coords);
                    $y = (float)array_shift($coords);

                    // add new point's coordinates
                    $current_point = [
                        $a*$x + $c*$y + $e,
                        $b*$x + $d*$y + $f,
                    ];
                    $new_coords = array_merge($new_coords, $current_point);

                    // same for 2nd point
                    $x = (float)array_shift($coords);
                    $y = (float)array_shift($coords);

                    // add new point's coordinates
                    $current_point = [
                        $a*$x + $c*$y + $e,
                        $b*$x + $d*$y + $f,
                    ];
                    $new_coords = array_merge($new_coords, $current_point);
                }

                // every other commands
                // @TODO: handle 'a,c,s' (elliptic arc curve) commands
                // cf. http://www.w3.org/TR/SVG/paths.html#PathDataCurveCommands
                else {
                    $x = (float)array_shift($coords);
                    $y = (float)array_shift($coords);

                    // add new point's coordinates
                    $current_point = [
                        $a*$x + $c*$y + $e,
                        $b*$x + $d*$y + $f,
                    ];
                    $new_coords = array_merge($new_coords, $current_point);
                }
            }

            $instruction = $i . implode(',', $new_coords);

            // remove useless commas
            $instruction = preg_replace('/,-/', '-', $instruction);
        }

        return implode('', $instructions);
    }



    /**
     *
     * Short-hand methods
     *
     */


    /**
     * Return full SVG XML
     * @return string
     */
    public function asXML(): string
    {
        return $this->svg->asXML();
    }

    /**
     * Adds an attribute to the SVG
     * @param string $key
     * @param string $value
     */
    public function addAttribute(string $key, string $value): void
    {
        $this->svg->addAttribute($key, $value);
    }
}
