<?php

/**
 * PlugEditor class
 *
 * Get access to javascript WYSIWYG Editors
 *
 * @author Evgeny Shilov <evgeny@internet-menu.ru>
 * @version 1.0
 */
class PlugEditor {

    public static $inited = array();

    public static function tinymce($type, $css_class = '') {
        if (SF_INADMIN) {
            SFAdminPage::js('/plug/editor/tinymce/tiny_mce.js', 10);
        } else {
            SFPage::js('/plug/editor/tinymce/tiny_mce.js', 10);
        }

        if (!empty(self::$inited[$type])) {
            return;
        }
        switch ($type) {
            case 'mini' :
                echo '
          <script type="text/javascript">
            if(typeof tinyMCE === "undefined"){
                var s = document.createElement("script");
                s.type = "text/javascript";
                s.src = "/plug/editor/tinymce/tiny_mce.js";
                $("head").append(s);
            }
          tinyMCE.init({
            convert_urls : false,
            
            // General options
            mode : "textareas",
            ' . ($css_class ? 'editor_selector : "' . $css_class . '",' : '') . '
            theme : "advanced",
            plugins : "autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
            language:"ru",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,forecolor,backcolor,|,formatselect,fontsizeselect",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

            // Skin options
            skin : "o2k7",
            skin_variant : "silver",

            // Example content CSS (should be your site CSS)
            content_css : "/theme/default/css/editor.css?hash='.md5(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/theme/default/css/editor.css')).'",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "js/template_list.js",
            external_link_list_url : "js/link_list.js",
            external_image_list_url : "js/image_list.js",
            media_external_list_url : "js/media_list.js",

          });
          </script>';
                break;
            case 'full' :
                echo '
          <script type="text/javascript">
            if(typeof tinyMCE === "undefined"){
                var s = document.createElement("script");
                s.type = "text/javascript";
                s.src = "/plug/editor/tinymce/tiny_mce.js";
                $("head").append(s);
            }
          tinyMCE.init({
            convert_urls : false,

            // General options
            language:"ru",
            mode : "textareas",
            ' . ($css_class ? 'editor_selector : "' . $css_class . '",' : '') . '
            theme : "advanced",
            plugins : "images,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

            // Theme options
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect,|,forecolor,backcolor",
            theme_advanced_buttons2 : "pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,code,preview,|,sub,sup,|,charmap,iespell,media,youtube,|,fullscreen",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,|,styleprops,spellchecker,|,cite,|,visualchars,nonbreaking,blockquote,pagebreak,insertfile,insertimage",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,
            
            setup : function(ed) {

                ed.addButton("youtube", {
                    title : "Вставить ролик youtube",
                    image : "/plug/editor/tinymce/youtube.png",
                    onclick : function() {
                        if(content = prompt("Вставьте код ролика")){
                            ed.execCommand("mceInsertContent",false,content);
                        }
                    }
                });

            },

            // Skin options
            skin : "o2k7",
            skin_variant : "silver",

            // Example content CSS (should be your site CSS)
            content_css : "/theme/default/css/editor.css?hash='.md5(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/theme/default/css/editor.css')).'",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "js/template_list.js",
            external_link_list_url : "js/link_list.js",
            external_image_list_url : "js/image_list.js",
            media_external_list_url : "js/media_list.js",

          });
          </script>';
                break;
            case 'mega' :
                echo '
          <script type="text/javascript">
            if(typeof tinyMCE === "undefined"){
                var s = document.createElement("script");
                s.type = "text/javascript";
                s.src = "/plug/editor/tinymce/tiny_mce.js";
                $("head").append(s);
            }
          tinyMCE.init({
            convert_urls : false,

            // General options
            language:"ru",
            mode : "textareas",
            ' . ($css_class ? 'editor_selector : "' . $css_class . '",' : '') . '
            theme : "advanced",
            plugins : "images,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",

            // Theme options
            theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
            theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,images,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
            theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
            theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

            // Skin options
            skin : "o2k7",
            skin_variant : "silver",

            // Example content CSS (should be your site CSS)
            content_css : "/theme/default/css/editor.css?hash='.md5(file_get_contents($_SERVER['DOCUMENT_ROOT'].'/theme/default/css/editor.css')).'",

            // Drop lists for link/image/media/template dialogs
            template_external_list_url : "js/template_list.js",
            external_link_list_url : "js/link_list.js",
            external_image_list_url : "js/image_list.js",
            media_external_list_url : "js/media_list.js",

          });
          </script>';
                break;
        }
        self::$inited[$type] = true;
    }

}
