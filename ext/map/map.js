ymaps.ready(init);
var myMap;
function init() {
  myMap = new ymaps.Map("map-42", {
      center: [57.999539, 56.158827],
      zoom: 16
  });
  myMap.behaviors.disable('scrollZoom');
  var myPlacemark = new ymaps.Placemark([57.999539, 56.158827], {balloonContent: '<b>Стоматология «Евро-Дент»</b><br />г. Пермь, мр. Парковый, ул. Строителей, 48<br /> Тел. (342) 229-40-81'});
  myMap.geoObjects.add(myPlacemark);
}