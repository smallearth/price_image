<?php
/**
 * 使用示例
 */

use Price\Price;

require 'src/Price.php';

/**
 * 根目录
 */
$root = dirname(__FILE__);

/**
 * 字体目录
 */
$fontNumber     = $root . "/resources/font/tahoma.ttf";
$fontNumberBold = $root . "/resources/font/tahomabd.ttf";
$fontCommon     = $root . "/resources/font/Hiragino-Sans-GB-W3.ttf";
$fontCommonBold = $root . "/resources/font/Hiragino-Sans-GB-W6.ttf";


/**
 * 实例
 */
$price = new Price();
/**
 * 设置模板
 */
$price->setTemplate(Price::$TEMPLATE_PC_LIST);
/**
 * 设置图片宽度
 */
//$price->setImgWidth(1000);
/**
 * 设置图片高度
 */
$price->setImgHeight(60);
/**
 * 设置普通文字大小
 */
$price->setCommonFontSize(16);
/**
 * 设置价格文字大小
 */
$price->setPriceFontSize(20);
/**
 * 设置字体
 */
$price->setFontNumber($fontNumber);
$price->setFontNumberBold($fontNumberBold);
$price->setFontCommon($fontCommon);
$price->setFontCommonBold($fontCommonBold);

/**
 * 设置价格
 */
for ($i = 0; $i < 10; $i++) {
    $position = $price->addPrice(rand(10000, 99999), rand(1, 10), "元/月", "元/平/日");
}
/**
 * 获取图片
 */

$positions = $price->getPosition();
$image     = $price->getPriceImg();

/**
 * 输出
 */
header("Content-Type: image/png");
$res = imagepng($image);