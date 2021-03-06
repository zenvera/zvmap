<?php
if (!function_exists('getallheaders'))
{
	function getallheaders()
	{
		$headers = '';
		foreach ($_SERVER as $name => $value)
		{
			if (substr($name, 0, 5) == 'HTTP_')
			{
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}

$x = intval($_GET["x"]);
$y = intval($_GET["y"]);
$z = intval($_GET["z"]);

if(!is_numeric($x)||!is_numeric($y)|!is_numeric($z))
	return;

$cache_root = "/home/zenvera/public_html/map2/cache";
$cache_dir =  $cache_root . "/" . $z . "/" . $x;
$cache = $cache_dir . "/" . $y . ".png";

if(!file_exists($cache_dir)) {
	mkdir($cache_dir, 0777, true);
}

if(!file_exists($cache)) {
	$tileX = 320;
	$tileY = 256;

	$img = imagecreatefrompng("/home/zenvera/public_html/img/facet-cropped-edgeclean.png");
	$resized = imagecreatetruecolor($tileX, $tileY);
	if($z <= 4)
		imagecopyresized($resized, $img, 0, 0, $x * ($tileX << (4-$z)), $y * ($tileY << (4-$z)), $tileX, $tileY, $tileX << (4-$z), $tileY << (4-$z));
	else
		imagecopyresized($resized, $img, 0, 0, $x * ($tileX >> ($z-4)), $y * ($tileY >> ($z-4)), $tileX, $tileY, $tileX >> ($z-4), $tileY >> ($z-4));
	imagepng($resized, $cache);
}

$headers = getallheaders();

if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($cache))) {
  header("Last-Modified: " . gmdate('D, d M Y H:i:s', filemtime($cache)) . " GMT", true, 304);
} else {
  header("Last-Modified: " . gmdate('D, d M Y H:i:s', filemtime($cache)) . " GMT", true, 200);
  header("Content-Length: " . filesize($cache));
  header("Content-Type: image/png");
  $fp = fopen($cache, 'rb');
  fpassthru($fp);
}

//header("Content-Type: image/png");
//header("Content-Length: " . filesize($cache));
//$fp = fopen($cache, 'rb');
//fpassthru($fp);
?>
