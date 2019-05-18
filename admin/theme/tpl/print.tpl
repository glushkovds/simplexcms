<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<?php
//PlugJQuery::jquery();
SFAdminPage::css('/admin/theme/css/print.css');
//SFPage::js('/theme/default/js/default.js');
SFAdminPage::meta();
?>
</head>

<body>
  <div style="width:700px;margin:0px auto">
     <?php SFAdminPage::content();?>
  </div>
</html>
