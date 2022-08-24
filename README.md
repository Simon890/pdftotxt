# PDF2TXT

### Convert PDFs to TEXT in PHP

This class converts your PDF files to plain TXT using poppler-utils.

### Important Notes

Please see how to use below.

### Requirements

#### 1. Install Poppler-Utils
##### Windows
You can download poppler here: http://blog.alivate.com.au/poppler-windows/.
After download, extract it.
#### 2. Configure PHP
##### Example:
```php
<?php
require_once("./src/Pdf2Txt.php");
$pdf2Txt = new Pdf2Txt("path/to/file.pdf", [
  "pdftotxt_path" => "poppler/bin/pdftotext.exe" //Here goes the location of your binary,
  "pdfinfo_path" => "poppler/bin/pdfinfo.exe"
]);
$txt = $pdf2Txt->getTxt();
echo $txt;
```
