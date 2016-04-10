<?php

class PoParser {

    public $error;
    public $uploadPath;
    private $uploadedFile;
    private $flashMessage;

    public function __construct() {
        //making object of Flash messages
        include_once 'PoParser/FlashMessages.php';
        if (!session_id())
            @session_start();
        $this->flashMessage = new FlashMessages();
        //setting upload path
        $this->uploadPath = $this->createDirectory(getcwd() . DIRECTORY_SEPARATOR . "uploads");
    }

    /**
     * File will be uploaded here and parse 
     */
    public function uploadParseFile() {

        if (isset($_FILES['po_file'])) {

            //if no error found of file extension then Go proceed
            if ($this->validateExtension($_FILES['po_file']['name']) == "") {



                //uploaded file will be used to parse data
                $this->uploadedFile = str_replace(" ", "_", $this->uploadPath . $_FILES["po_file"]['name']);

                if (move_uploaded_file($_FILES['po_file']['tmp_name'], $this->uploadedFile)) {
                    
                }
                $include_header = isset($_POST['include_header']) ? 1 : 0;

                $this->parsePoFile($this->uploadedFile, $include_header);
            } else {
                
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
            return $this->error = "InValid Extension";
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
        //genreating resx file
        $resx = str_replace("." . pathinfo(basename($file), PATHINFO_EXTENSION), "", basename($file));
        $this->generateXmlFile($resx, $xml_generated);

        //seting flash message

        $this->flashMessage->success("File has been uploaded and converted");


        $this->redirectToSamePage($resx);
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
        $myfile = fopen($this->uploadPath . $file . ".resx", "w") or die("Unable to open file!");

        fwrite($myfile, $content);

        fclose($myfile);
    }

    /**
     * redirect to same path
     */
    private function redirectToSamePage($resx_file = "") {
        header('Location: ' . $_SERVER['PHP_SELF'] . "?resx_file=" . $resx_file);
    }

    /**
     * 
     * @param type $key
     * @param type $value
     */
    private function setFlash($key, $value) {

        if (!session_id())
            @session_start();
        $_SESSION['flash'][$key] = $value;
    }

    /**
     * print message that has done or not
     * @param type $key
     */
    public function printFlash($key) {

        $this->flashMessage->display();
    }

    /**
     * Get curent host with current directory
     */
    public function getCurrentHost() {
        return "http://" . $_SERVER['SERVER_NAME'] . dirname($_SERVER['REQUEST_URI']) . "/";
    }
    /**
     * Get uploaded url
     * @return type
     */
    public function getUploadedUrl() {
        return $this->getCurrentHost() . "uploads/";
    }

}
