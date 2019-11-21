<?php
define('MAX_FILE_LIMIT', 1024 * 1024 * 2);//2 Megabytes max html file size

function sanitizeFileName($fileName)
{
	//sanitize, remove double dot .. and remove get parameters if any
	$fileName = __DIR__ . '/' . preg_replace('@\?.*$@' , '', preg_replace('@\.{2,}@' , '', preg_replace('@[^\/\\a-zA-Z0-9\-\._]@', '', $fileName)));
	return $fileName;
}

$html = "";
if (isset($_POST['startTemplateUrl']) && !empty($_POST['startTemplateUrl'])) 
{
	$startTemplateUrl = sanitizeFileName($_POST['startTemplateUrl']);
	$html = file_get_contents($startTemplateUrl);
} else if (isset($_POST['html']))
{
	$html = substr($_POST['html'], 0, MAX_FILE_LIMIT);
}
$html = preg_replace("/contenteditable=[\"|'][^'\"]*[\"|']/", "", $html);
/* TODO:
$dom = new DOMDocument();
$dom->loadHTML($html);

$xpath = new DOMXPath($dom);
$parentNode = $xpath->query("//[@id='sensors']");

$html = '';
foreach ($parentNode->item(0)->childNodes as $node) {
    $html .= $node->ownerDocument->saveHtml($node);
}

echo $html;
exit;
*/
$fileName = sanitizeFileName($_POST['fileName']);

$fileName = "../html/".str_replace("php","html", basename($fileName));
if (file_put_contents($fileName, $html))
	echo basename($fileName);
else 
	echo 'Error saving file '  . $fileName;
