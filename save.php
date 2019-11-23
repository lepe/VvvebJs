<?php
require_once(__DIR__ . "/../inc/" . "pages.php");
require_once(__DIR__ . "/../inc/" . "links.php");
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
} else if (isset($_POST['html'])) {
	$html = substr($_POST['html'], 0, MAX_FILE_LIMIT);
}
// Clear edition attributes:
$html = preg_replace("/contenteditable=[\"|'][^'\"]*[\"|']/", "", $html);
$html = preg_replace("/spellcheckker=[\"|'][^'\"]*[\"|']/", "", $html);

$fileName = sanitizeFileName($_POST['fileName']);
$pageID = str_replace(".php","", basename($fileName));

$ok = key_exists($pageID, $PAGES);
if($ok) {
    // TODO: do the same for footer and header
    $dom = new DOMDocument();
    $dom->loadHTML($html);
    $xpath = new DOMXPath($dom);

    foreach($PAGES[$pageID] as $page) {
        if($ok) {
            $lookFor = "";
            if(key_exists($page, $TAGS)) {
                $lookFor = $TAGS[$page];
            } else {
                if($page == "header" || $page == "footer") {
                    $lookFor = "//$page";
                } else {
                    $lookFor = "//section[@id='$page']";
                }
            }
            $parentNode = $xpath->query("//$lookFor");
            if($parentNode) {
                $html = $dom->saveHtml($parentNode->item(0));
                $fileName = "../html/$page.html";
                $ok = (file_put_contents($fileName, $html));
            }
        }
    }
}
if($ok) {
    echo 'Saved successfully!';
} else {
    echo 'Error saving page';
}
