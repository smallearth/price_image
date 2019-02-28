<?php
/**
 * @name   ${NAME}
 * @author Jack
 * @desc
 * @see    http://www.php.net/manual/en/class.yaf-controller-abstract.php
 */

require 'src/Price.php';
//use Price\src\Price;

$fontNumber     = "/Users/Jack/Code/Price/resources/font/tahoma.ttf";
$fontNumberBold = "/Users/Jack/Code/Price/resources/font/tahomabd.ttf";
$fontCommon     = "/Users/Jack/Code/Price/resources/font/Hiragino-Sans-GB-W3.ttf";
$fontCommonBold = "/Users/Jack/Code/Price/resources/font/Hiragino-Sans-GB-W6.ttf";

$price = new Price();
$price->setTemplate(Price::$TEMPLATE_PC_LIST);
$price->setImgWidth(1000);
$price->setImgHeight(60);
$price->setCommonFontSize(16);
$price->setPriceFontSize(20);
$price->setFontNumber($fontNumber);
$price->setFontNumberBold($fontNumberBold);
$price->setFontCommon($fontCommon);
$price->setFontCommonBold($fontCommonBold);

$prices = [[10000, 10], [2222, 10], [22232, 7.8]];

foreach ($prices as $item) {
    $position = $price->addPrice($item[0], $item[1], "元/月", "元/平/日");
}
$image = $price->getPriceImg();

//$position = $price->getPosition();
//die(json_encode($position));

header("Content-Type: image/png");
$res = imagepng($image);