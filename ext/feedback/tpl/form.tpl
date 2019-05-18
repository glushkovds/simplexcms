<?php
PlugAlert::flush();
if(!$sended) {
  echo $this->params['text_before'];
  $form->html();
}
