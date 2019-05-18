<?php

class ModMap extends SFModBase {

    protected function content() {
        SFPage::js('http://api-maps.yandex.ru/2.1/?lang=ru_RU');
        SFPage::js('/ext/map/map'.$this->id.'.js');

        $name = SFCore::siteParam('site_name');
        $phone = SFCore::siteParam('phone');
        $city = SFCore::siteParam('city');
        $address = SFCore::siteParam('address');
        
        $geocode = $city . ' ' . $address;
        $hint = '<b>'.$name.'</b><br />'.($city ? 'г. '.$city.', ' : '').$address.'<br />Тел. '.$phone;
        
        if(!empty($this->params['hint'])) {
            $hint = $this->params['hint'];
        }
        
        if(!empty($this->params['address'])) {
            $geocode = $this->params['address'];
        }
        
        $hash = md5($name.$phone.$geocode.$hint);
        
        if ($hash!=$this->params['hash']) {
            $geo = json_decode(file_get_contents('http://geocode-maps.yandex.ru/1.x/?format=json&geocode=' . urlencode($geocode)), 1);
            if (isset($geo['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos'])) {
                $pos = explode(' ', $geo['response']['GeoObjectCollection']['featureMember'][0]['GeoObject']['Point']['pos']);

                $rows = array();
                $rows[] = 'ymaps.ready(init);';
                $rows[] = 'var myMap;';
                $rows[] = 'function init() {';
                $rows[] = '  myMap = new ymaps.Map("map-'.$this->id.'", {';
                $rows[] = '      center: [' . $pos[1] . ', ' . $pos[0] . '],';
                $rows[] = '      zoom: 16';
                $rows[] = '  });';
                $rows[] = "  myMap.behaviors.disable('scrollZoom');";
                $rows[] = '  var myPlacemark = new ymaps.Placemark([' . $pos[1] . ', ' . $pos[0] . '], {balloonContent: \'' . str_replace("\r\n", "<br />", $hint) . '\'});';
                $rows[] = '  myMap.geoObjects.add(myPlacemark);';
                $rows[] = '}';
                $content = join("\r\n", $rows);
                file_put_contents('ext/map/map'.$this->id.'.js', $content);
                
                $this->params['hash'] = $hash;
                $q="UPDATE module_item SET params = '".SFDB::escape(serialize($this->params))."' WHERE item_id=".$this->id;
                SFDB::query($q);
            }
        }

        echo '<div id="map-'.$this->id.'" style="'.$this->params['style'].'"></div>';
    }

}
