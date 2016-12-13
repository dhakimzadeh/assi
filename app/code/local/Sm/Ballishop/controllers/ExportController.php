<?php
/*------------------------------------------------------------------------
 # SM Ballishop - Version 1.0
 # Copyright (c) 2014 The YouTech Company. All Rights Reserved.
 # @license - Copyrighted Commercial Software
 # Author: YouTech Company
 # Websites: http://www.magentech.com
-------------------------------------------------------------------------*/

class Sm_Ballishop_ExportController extends Mage_Core_Controller_Front_Action {

    public function exportAction(){
        $type = $this->getRequest()->getParam('type');
        $ids  = $this->getRequest()->getParam('ids');
        
        if ($type == 'pages' || $type == 'blocks'){
            if (count($ids) > 0){
                $filename = $type . '.xml';
                // create xml document
                $xmldoc = new DOMDocument();
                $xmldoc->formatOutput = true;
                 
                // create root nodes
                $root = $xmldoc->createElement("root");
                $xmldoc->appendChild($root);
    
                $type_root = $xmldoc->createElement($type);
                $root->appendChild($type_root);
                 
                try {
                    // $type_root = $xml->getNode($type);
                    $model = $type == "pages" ? "cms/page" : "cms/block";
                    foreach ($ids as $id){
                        $cms_obj = Mage::getModel($model)->load($id);
                        if ($cms_obj instanceof Mage_Cms_Model_Page){
                             
                            $cms_obj_xml = $xmldoc->createElement("cms_page");
                             
                            $tmp = $xmldoc->createElement("title");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->title));
                            $cms_obj_xml->appendChild($tmp);
    
                            $tmp = $xmldoc->createElement("root_template");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->root_template));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $tmp = $xmldoc->createElement("identifier");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->identifier));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $tmp = $xmldoc->createElement("content_heading");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->content_heading));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $content_html = $xmldoc->createDocumentFragment();
                            $content_html->appendXML('<![CDATA['.$cms_obj->content.']]>');
                            $tmp = $xmldoc->createElement("content");
                            $tmp->appendChild($content_html);
                            $cms_obj_xml->appendChild($tmp);
                             
                            $tmp = $xmldoc->createElement("is_active");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->is_active));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $layout_update_xml = $xmldoc->createDocumentFragment();
                            $layout_update_xml->appendXML('<![CDATA['.$cms_obj->layout_update_xml.']]>');
                            $tmp = $xmldoc->createElement("layout_update_xml");
                            $tmp->appendChild($layout_update_xml);
                            $cms_obj_xml->appendChild($tmp);
                             
                            $tmp = $xmldoc->createElement("store_id");
                            $tmp->appendChild($xmldoc->createTextNode(implode(",", $cms_obj->store_id)));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $type_root->appendChild($cms_obj_xml);
                        } else if ($cms_obj instanceof Mage_Cms_Model_Block){
                             
                            $cms_obj_xml = $xmldoc->createElement("cms_block");
    
                            $tmp = $xmldoc->createElement("title");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->title));
                            $cms_obj_xml->appendChild($tmp);
    
                            $tmp = $xmldoc->createElement("identifier");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->identifier));
                            $cms_obj_xml->appendChild($tmp);
                             
                            $content_html = $xmldoc->createDocumentFragment();
                            $content_html->appendXML('<![CDATA['.$cms_obj->content.']]>');
                            $tmp = $xmldoc->createElement("content");
                            $tmp->appendChild($content_html);
                            $cms_obj_xml->appendChild($tmp);
    
                            $tmp = $xmldoc->createElement("is_active");
                            $tmp->appendChild($xmldoc->createTextNode($cms_obj->is_active));
                            $cms_obj_xml->appendChild($tmp);
							
                            $tmp = $xmldoc->createElement("store_id");
                            $tmp->appendChild($xmldoc->createTextNode(implode(",", $cms_obj->store_id)));
                            $cms_obj_xml->appendChild($tmp);							
                             
                            $type_root->appendChild($cms_obj_xml);
                        }
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
                ob_end_clean();
                $this->_prepareDownloadResponse($filename, $xmldoc->saveXML());
            } else {
                die("No items were selected");
            }
        } else if ($type == 'configuration'){
            
        } else {
            die("Invalid Type!");
        }
    }
    
    public function indexAction(){
        ob_start();
        echo '<html><head>
            <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.2/css/bootstrap.min.css" rel="stylesheet" />
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
            <script type="text/javascript">
	            jQuery(function(){
	    
                    $("#all_pages").click(function(){
                        var allchecked = this.checked;
                        if (allchecked){
                            $("#table_pages input.ids").each(function(){
                                if (!this.checked){
                                    $(this).parents("tr").trigger("click");
                                }
                            });
                        } else {
                            $("#table_pages input.ids").each(function(){
                                if (this.checked){
                                    $(this).parents("tr").trigger("click");
                                }
                            });
                        }
                    });
                    $("#all_blocks").click(function(){
                        var allchecked = this.checked;
                        if (allchecked){
                            $("#table_blocks input.ids").each(function(){
                                if (!this.checked){
                                    $(this).parents("tr").trigger("click");
                                }
                            });
                        } else {
                            $("#table_blocks input.ids").each(function(){
                                if (this.checked){
                                    $(this).parents("tr").trigger("click");
                                }
                            });
                        }
                    });
                        
                    $("#table_pages tbody tr").click(function(e){
                        if ($(e.target).attr("type") == "checkbox") return;
                        $("input.ids", this).each(function(){
                            $(this).trigger("click");
                        });
                    });
                    $("#table_pages input.ids").change(function(e){
                        var active = this.checked;
                        $(this).parent().parent()[active?"addClass":"removeClass"]("success");
                        $(this).blur();
                    });
                    
                    $("#table_blocks tbody tr").click(function(e){
                        if ($(e.target).attr("type") == "checkbox") return;
                        $("input.ids", this).each(function(){
                            $(this).trigger("click");
                        });
                    });
                    $("#table_blocks input.ids").change(function(e){
                        var active = this.checked;
                        $(this).parent().parent()[active?"addClass":"removeClass"]("success");
                        $(this).blur();
                    });
                });
            </script>
	        ';
        echo '</head><body>';
        echo '<div class="container">';
        echo '<h2 class="page-header">Export CMS Page/Static Block</h2>';
        echo '<h3>CMS Pages</h3>';
        echo '<form method="POST" action="'.Mage::getBaseUrl().'ballishop/export/export/">
            <table id="table_pages" class="table table-bordered table-condensed">
	        <thead>
	            <tr>
	                <th style="text-align:center;">#</th>
	                <th>Title</th>
	                <th>URL Key</th>
	                <th><input type="checkbox" id="all_pages" title="Select All"></th>
	            </tr>
	        </thead>
	        <tfoot>
	            <tr>
	                <td colspan="6" class="text-right"><button class="btn btn-primary" type="submit">Export CMS Pages</button></td>
	            </tr>
	        </tfoot>
	        <tbody>
	        ';
        $pages = Mage::getModel("cms/page")->getCollection();
        $i = 0;
        foreach ($pages as $key => $page){
            $i++;
            $frm = '<tr style="cursor: pointer;">
	            <td style="text-align:center;">%s</td>
	            <td>%s</td>
	            <td>%s</td>
	            <td>%s</td>
	            </tr>';
            $chk = '<input type="checkbox" class="ids" name="ids[]" value="'.$page->getId().'" title="'.$page->getId().'">';
            echo sprintf($frm, $i, $page->title, $page->identifier, $chk);
        }
         
        echo '</tbody>
	        </table>
	        <input type="hidden" name="type" value="pages">
	        </form>';
        
        echo '<br/><h3>Static Blocks</h3>';
        echo '<form method="POST" action="'.Mage::getBaseUrl().'ballishop/export/export/">
            <table id="table_blocks" class="table table-bordered table-condensed">
	        <thead>
	            <tr>
	                <th style="text-align:center;">#</th>
	                <th>Title</th>
	                <th>Identifier</th>
	                <th><input type="checkbox" id="all_blocks" title="Select All"></th>
	            </tr>
	        </thead>
	        <tfoot>
	            <tr>
	                <td colspan="6" class="text-right"><button class="btn btn-primary" type="submit">Export Static Blocks</button></td>
	            </tr>
	        </tfoot>
	        <tbody>
	        ';
        $blocks = Mage::getModel("cms/block")->getCollection();
        $i = 0;
        foreach ($blocks as $key => $block){
            $i++;
            $frm = '<tr style="cursor: pointer;">
	            <td style="text-align:center;">%s</td>
	            <td>%s</td>
	            <td>%s</td>
	            <td>%s</td>
	            </tr>';
            $chk = '<input type="checkbox" class="ids" name="ids[]" value="'.$block->getId().'">';
    
            echo sprintf($frm, $i, $block->title, $block->identifier, $chk);
        }
    
        echo '</tbody>
	        </table>
	        <input type="hidden" name="type" value="blocks">
	        </form>';
         
        echo '</div>';
        echo '</body>';
        $this->getResponse()->setBody(ob_get_clean());
    }
}