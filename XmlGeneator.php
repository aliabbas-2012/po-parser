<?php

/**
 *
 */
class XmlGeneator {

    /**
     * Used for generating resx file as 
     * @param type $entries
     * @param type $header
     * @return type
     */
    public function generate_resx($entries = array(), $header = array()) {

        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root></root>');

        echo "<pre>";
        print_r($entries);
        echo "</pre>";


        foreach ($entries as $key => $entry) {
            if (isset($key) && !empty($key)) {
                $title_node = $xml->addChild('data');
                $title_node->addAttribute('name', $key);
                $title_node->addAttribute('content_context', 'Content');
                //make changes in future
                $title_node->addAttribute('content_context_url', 'http://local.apidocs.strakertranslations.com/#');
                $title_node->addAttribute('post_id', "post_id");
                $title_node->addAttribute('post_type', "post_type");
                $title_node->addAttribute('post_name', $key);

                //adding references
                $reference = "";
                foreach ($entry['references'] as $ref) {
                    if (!empty($msg_str))
                        $reference.= $ref;
                }
                $title_node->addAttribute('references', $reference);
                //populating values against data
                $value = "";
                foreach ($entry['msgstr'] as $msg_str) {
                    if (!empty($msg_str))
                        $value.= $msg_str;
                    $value.= (($msg_str));
                }
                echo $value;
                echo "<br/>";

               
                $title_value = $title_node->addChild('value', "<![CDATA[" . $value . "]]>");
//                $title_value = $title_node->addChild('value', iconv('ISO-8859-1', 'UTF-8', $value));
            }
        }


        $resx = $xml->asXML();

        return $resx;
    }

    public function add_CData($cdata_text) {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
    }

}

?>
