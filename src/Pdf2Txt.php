<?php
class Pdf2Txt {
    private $defaultOptions = [
        "pdftotxt_path" => "poppler/bin/pdftotext.exe",
        "pdfinfo_path" => "poppler/bin/pdfinfo.exe",
        "outDir" => "app/pdf2txt",
        "enc" => "UTF-8",
        "clearAfter" => true
    ];
    private $options = [];
    private $path;

    public function __construct($path, $options = []) {
        $this->path = $path;
        $this->setOptions(\array_replace_recursive($this->defaultOptions, $options));
    }

    public function setOptions($key, $value = null) {
        if(is_array($key)) {
            $this->options = array_replace_recursive($this->options, $key);
        } elseif(is_string($key)) {
            $this->options[$key] = $value;
        }
    }

    public function getOptions() {
        return $this->options;
    }

    public function setOutDir($outDir) {
        $this->setOptions("outDir", $outDir);
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public function getTxt() {
        $outDir = $this->getOptions()["outDir"] ? $this->getOptions()["outDir"] : dirname(__FILE__) . uniqid();
        if(!\file_exists($outDir)) mkdir($outDir, 0777, true);

        $this->setOutDir($outDir);
        $pathInfo = pathinfo($this->path);
        $basePath = $this->getOptions()["outDir"] . "/" . $pathInfo["filename"];
        $cmd = $this->getCommand();
        exec($cmd);
        var_dump($res);
        $txt = file_get_contents($this->generateOutPath());
        if($this->getOptions()["clearAfter"])
            $this->clearOutDir();
        return $txt;
    }

    public function getInfo() {
        $cmd = escapeshellarg($this->getOptions()["pdfinfo_path"]) . " " . \escapeshellarg($this->path);
        $infoStr = shell_exec($cmd);
        $options = explode("\n", $infoStr);
        $info = [];
        foreach ($options as &$opt) {
            if(!empty($opt)) {
                list($key, $value) = explode(":", $opt);
                $info[str_replace([" "], ["_"], strtolower($key))] = trim($value);
            }
        }
        return $info;
    }

    private function getCommand() {
        $opt = $this->getOptions();
        $cmd = escapeshellarg($opt["pdftotxt_path"]) . " " . $this->generateOptions() . " " . " " . escapeshellarg($this->generateOutPath());
        return $cmd;
    }

    private function generateOutPath() {
        return $this->getOptions()["outDir"] . "/" . preg_replace("/\.pdf$/", "", basename($this->path)) . ".txt";
    }

    private function generateOptions() {
        $generated = [];
        $opts = $this->getOptions();
        $generated[] = "-enc " . $opts["enc"];
        $generated[] = escapeshellarg($this->path);
        return implode(" ", $generated);
    }

    private function clearOutDir() {
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->getOptions()["outDir"], \FilesystemIterator::SKIP_DOTS));
        foreach ($files as $file) {
            $path = (string)$file;
            $basename = basename($path);
            if($basename != "..") {
                if(is_file($path) && file_exists($path))
                    unlink($path);
                elseif(is_dir($path) && file_exists($path))
                    rmdir($path);
            }
        }
    }

}