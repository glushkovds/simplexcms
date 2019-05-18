<?php
header('Content-type: image/gif');
header('Cache-Control: no-store, no-cache, must- revalidate');
session_start();
/*---- УСТАНОВКА ПАРАМЕТРОВ ----------------------------------------*/
$cnt = 4; 			// Количество символов
$symbols = "0123456789";	// Эти символы будут на картинке

$w=94;			// ШИРИНА
$h=40;			// ВЫСОТА
$per=0.06;		// ПРОЦЕНТ ТОЧЕК

$R1=225;	$G1=225;	$B1=225;	// ЦВЕТ ЗАЛИВКИ
$R2=70;  $G2=70;  $B2=70;   // ЦВЕТ ЦИФР
$R3=100;  $G3=100; 	$B3=100;   // ЦВЕТ ТОЧЕК
$R4=100;  $G4=100; 	$B4=100;	// ЦВЕТ ЛИНИЙ


/*---- ГЕНЕРИРУЕМ ЧИСЛО -----------------------------------------------*/
$spamcode='';
for($i=0; $i<$cnt; $i++) {
	$spamcode.= $symbols{rand(0,strlen($symbols)-1)};
}
$_SESSION['spamcode'] = $spamcode;

/*---- СОЗДАЕМ ИЗОБРАЖЕНИЕ И УСТАНАВЛИВАЕМ ЦВЕТА -------*/
$img = imagecreatetruecolor($w,$h);
$color1 = imagecolorallocate($img,$R1,$G1,$B1);
$color2 = imagecolorallocate($img,$R2,$G2,$B2);
$color3 = imagecolorallocate($img,$R3,$G3,$B3);
$color4 = imagecolorallocate($img,$R4,$G4,$B4);

imagefill($img,1,1,$color1);	// ЗАЛИВАЕМ ИЗОБРАЖЕНИЕ

/*---- ВЫВОДИМ ЧИСЛО НА КАРТИНКУ -------------------------------*/
for ($i=0, $n=strlen($spamcode); $i<$n; $i++) {
	$ch=$spamcode{$i};
	$ang=rand(0,1) ? rand(6,12) : -rand(6,12);	// УГОЛ НАКЛОНА ЦИФР
	imagettftext($img, 26, $ang, 4+$i*20, 32, $color2, dirname(__FILE__)."/cartoon8.ttf", $ch);
}
/*---- ЗАБРЫЗГИВАЕМ ТОЧКАМИ ИЗОБРАЖЕНИЕ --------------------*/
for ($i=0, $n=(int)$w*$h*$per; $i<$n; $i++) {
	$x=rand(0,$w);
	$y=rand(0,$h);
	imagesetpixel($img, $x, $y, $color3);
}

for ($i=0,$a=4,$pi=3.1415926; $i<$w; $i++) {
	for ($j=-$a,$y=round($a*cos($pi*$i/17)); $j<$h+$a; $j+=10) {
		if($i%15<10) {
			if($j+$y>=0 && $j+$y<$h) {
				imagesetpixel($img,$i,$j+$y,$color4);
			}
		}
	}
}


imagegif($img);
