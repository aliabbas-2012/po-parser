<?php

class PoParser {

    public $error = '';

    /**
     * File will be uploaded here and parse 
     */
    public function uploadParseFile() {
        if (isset($_FILES['po_file'])) {
            //if no error found of file extension then Go proceed
            if ($this->validateExtension($_FILES['po_file']['name']) == "") {
                $upload_path = $this->createDirectory(getcwd() . DIRECTORY_SEPARATOR . "uploads");
                //uploaded file will be used to parse data
                $uploadedFile = str_replace(" ", "_", $upload_path . $_FILES["po_file"]['name']);
                if (move_uploaded_file($_FILES['po_file']['tmp_name'], $uploadedFile)) {
                    $include_header = isset($_POST['include_header']) ? 1 : 0;
                    $this->parsePoFile($uploadedFile, $include_header);
                }

                //if directory not exist then create for uploading file
            }
        }
    }

    /**
     * Get File Extension here
     * @param type $filename
     * @return type
     */
    private function validateExtension($filename) {
        if (strtolower(pathinfo($filename, PATHINFO_EXTENSION)) != "po") {
            $this->error = "InValid Extension";
        }
        return "";
    }

    /**
     * Create directory function
     * @param type $path
     */
    private function createDirectory($path) {
        if (!is_dir($path)) {
            mkdir($path);
        }
        return $path . DIRECTORY_SEPARATOR;
    }

    /**
     * This function will parse the the whole file
     * @param type $file
     * @param type $include_header
     */
    private function parsePoFile($file, $include_header) {
        include_once 'PoParser/Parser.php';
        include_once 'PoParser/Entry.php';
        include_once 'XmlGeneator.php';
        $parser = new PoParser\Parser();
        $parser->read($file);
        $entries = $parser->getEntriesAsArrays();
        $headerInformation = array();
        if ($include_header) {
            $headerInformation = $this->getHeaderInformatin($entries);
        }
       

        $xmlGen = new XmlGeneator();
        $xml_generated = $xmlGen->generate_resx($entries, $headerInformation);
        
        
       
        
        $this->generateXmlFile("test",$xml_generated);

//        echo "<pre>";
//        print_r($xml_generated);
//        echo "</pre>";
    }

    public function getHeaderInformatin($entries) {
        foreach ($entries as $h => $entry) {
            return $entry;
            break;
        }
    }
    /**
     * 
     * @param type $file
     * @param type $content
     */
    public function generateXmlFile($file, $content) {
        $myfile = fopen($file . ".resx", "w") or die("Unable to open file!");

        fwrite($myfile, $content);

        fclose($myfile);
    }

}
