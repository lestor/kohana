<?php
/**
 * UTF8::strlen
 *
 * @package    Kohana
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _strlen($str)
{
	if (UTF8::is_ascii($str))
		return strlen($str);

	// https://php.watch/versions/8.2/utf8_encode-utf8_decode-deprecated#utf8_decode-php
	// https://github.com/symfony/polyfill-php72/blob/v1.26.0/Php72.php#L40-65
	for ($i = 0, $j = 0; $i < strlen($str); ++$i, ++$j) {
		switch ($str[$i] & "\xF0")
		{
			case "\xC0":
			case "\xD0":
				$c       = (ord($str[$i] & "\x1F" ) << 6) | ord($str[++$i] & "\x3F");
				$str[$j] = $c < 256 ? chr($c) : '?';
			break;
			case "\xF0":
				++$i;
				// no break
			case "\xE0":
				$str[$j] = '?';
				$i      += 2;
			break;
			default:
				$str[$j] = $str[$i];
		}
	}

	return strlen(substr($str, 0, $j));
}
