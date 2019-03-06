<?php
/**
 * 房源列表页价格防爬方案
 * @desc 将价格写在图片上，并返回详细坐标，前端可根据坐标读取价格，然后渲染在页面上
 */

namespace Price;

class Price
{
    /**
     * 模板定义
     * @var int
     */
    public static $TEMPLATE_PC_LIST = 1;
    public static $TEMPLATE_PC_DETAIL = 2;
    public static $TEMPLATE_M_LIST = 3;
    public static $TEMPLATE_M_DETAIL = 4;

    /**
     * 模板
     * @var
     */
    private $_template;

    /**
     * 数字字体
     * @var
     */
    private $_fontNumber;

    /**
     * 数字字体粗
     * @var
     */
    private $_fontNumberBold;

    /**
     * 普通字体
     * @var
     */
    private $_fontCommon;

    /**
     * 普通字体粗
     * @var
     */
    private $_fontCommonBold;

    /**
     * 价格字体大小
     * @var
     */
    private $_priceFontSize;

    /**
     * 普通价格大体大小
     * @var
     */
    private $_commonFontSize;

    /**
     * 图片宽度
     * @var int
     */
    private $_imgWidth = 0;

    /**
     * 图片高度
     * @var int
     */
    private $_imgHeight = 0;

    /**
     * 价格数据
     * @var array
     */
    private $_price = [];

    /**
     * 图片边缘边距
     * @var int
     */
    private $_padding = 10;
    private $_angle = 0;

    /**
     * 坐标
     * @var
     */
    private $_position;

    /**
     * 返回价格图片
     * @return null|resource
     */
    public function getPriceImg()
    {
        $image = null;
        try {
            if ($this->_template == self::$TEMPLATE_PC_LIST) {
                $image = $this->_createPCListPriceImg();
            } else if ($this->_template == self::$TEMPLATE_PC_DETAIL) {
                $image = $this->_createPCDetailPriceImg();
            } else if ($this->_template == self::$TEMPLATE_M_LIST) {
                $image = $this->_createMListPriceImg();
            } else if ($this->_template == self::$TEMPLATE_M_DETAIL) {
                $image = $this->_createMDetailPriceImg();
            }

        } catch (\Exception $e) {
            $image = null;
        }

        return $image;
    }

    /**
     * 设置价格
     *
     * @param        $price
     * @param        $unitPrice
     * @param string $priceText
     * @param string $unitPriceText
     *
     * @return array|null
     */
    public function addPrice($price, $unitPrice, $priceText = "", $unitPriceText = "")
    {
        $position = null;
        $width    = 0;
        $height   = 0;

        if (is_numeric($price) && isset($this->_template)) {
            switch ($this->_template) {
                case self::$TEMPLATE_PC_LIST :
                    $priceWidthHeight         = $this->_getFontWidthHeight($this->_priceFontSize, $this->_fontNumberBold, $price);
                    $priceTextWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $priceText);
                    $unitPriceWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontNumber, $unitPrice);
                    $unitPriceTextWidthHeight = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommon, $unitPriceText);

                    $width = $priceWidthHeight[0] + $priceTextWidthHeight[0];
                    if (($unitPriceWidthHeight[0] + $unitPriceTextWidthHeight[0]) > $width) {
                        $width = $unitPriceWidthHeight[0] + $unitPriceTextWidthHeight[0];
                    }
                    $height = $priceWidthHeight[1] + $unitPriceWidthHeight[1];

                    $width  += $this->_padding;
                    $height += $this->_padding * 4;

                    $position = [
                        [$this->_imgWidth, $height],
                        [$this->_imgWidth + $width, $height],
                    ];
                    break;
                case self::$TEMPLATE_PC_DETAIL :
                    $priceWidthHeight         = $this->_getFontWidthHeight($this->_priceFontSize, $this->_fontNumberBold, $price);
                    $priceTextWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $priceText);
                    $unitPriceWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontNumber, $unitPrice);
                    $unitPriceTextWidthHeight = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommon, $unitPriceText);

                    $width  = $priceWidthHeight[0] + $priceTextWidthHeight[0] + $unitPriceWidthHeight[0] + $unitPriceTextWidthHeight[0];
                    $height = $priceWidthHeight[1];

                    $width  += $this->_padding * 2;
                    $height += $this->_padding * 2;

                    $position = [
                        'price'     => [
                            [0, $height],
                            [$priceWidthHeight[0] + $priceTextWidthHeight[0] + $this->_padding, $height]
                        ],
                        'unitPrice' => [
                            [$priceWidthHeight[0] + $priceTextWidthHeight[0] + $this->_padding, $height],
                            [$width, $height]
                        ]
                    ];
                    break;
                case self::$TEMPLATE_M_LIST :
                    $priceWidthHeight     = $this->_getFontWidthHeight($this->_priceFontSize, $this->_fontNumberBold, $price);
                    $priceTextWidthHeight = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $priceText);

                    $width  = $priceWidthHeight[0] + $priceTextWidthHeight[0];
                    $height = $priceWidthHeight[1];

                    $width  += $this->_padding * 2;
                    $height += $this->_padding * 2;

                    $position = [
                        [$this->_imgWidth, $height],
                        [$this->_imgWidth + $width, $height],
                    ];
                    break;
                case self::$TEMPLATE_M_DETAIL :
                    $priceWidthHeight         = $this->_getFontWidthHeight($this->_priceFontSize, $this->_fontCommonBold, $price);
                    $priceTextWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $priceText);
                    $unitPriceWidthHeight     = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $unitPrice);
                    $unitPriceTextWidthHeight = $this->_getFontWidthHeight($this->_commonFontSize, $this->_fontCommonBold, $unitPriceText);

                    $width  = $priceWidthHeight[0] + $priceTextWidthHeight[0] + $unitPriceWidthHeight[0] + $unitPriceTextWidthHeight[0];
                    $height = $priceWidthHeight[1];

                    $width    += $this->_padding * 2;
                    $height   += $this->_padding * 2;
                    $position = [
                        'price'     => [
                            [0, $height],
                            [$priceWidthHeight[0] + $priceTextWidthHeight[0] + $this->_padding, $height]
                        ],
                        'unitPrice' => [
                            [$priceWidthHeight[0] + $priceTextWidthHeight[0] + $this->_padding * 1.5, $height],
                            [$width, $height]
                        ]
                    ];
                    break;
                default:
                    break;
            }

            $this->_imgWidth  += $width;
            $this->_imgHeight = $height;

            $item = [
                'price'         => $price,
                'unitPrice'     => $unitPrice,
                'width'         => $width,
                'position'      => $position,
                'priceText'     => $priceText,
                'unitPriceText' => $unitPriceText
            ];
            if (isset($priceWidthHeight)) {
                $item['priceWidthHeight'] = $priceWidthHeight;
            }
            if (isset($priceTextWidthHeight)) {
                $item['priceTextWidthHeight'] = $priceTextWidthHeight;
            }
            if (isset($unitPriceWidthHeight)) {
                $item['unitPriceWidthHeight'] = $unitPriceWidthHeight;
            }
            if (isset($unitPriceTextWidthHeight)) {
                $item['unitPriceTextWidthHeight'] = $unitPriceTextWidthHeight;
            }

            $this->_price[] = $item;
        }

        $this->_position[] = $position;

        return $position;
    }

    /**
     * @param mixed $fontNumber
     */
    public function setFontNumber($fontNumber)
    {
        $this->_fontNumber = $fontNumber;
    }

    /**
     * @param mixed $fontNumberBold
     */
    public function setFontNumberBold($fontNumberBold)
    {
        $this->_fontNumberBold = $fontNumberBold;
    }

    /**
     * @param mixed $fontCommon
     */
    public function setFontCommon($fontCommon)
    {
        $this->_fontCommon = $fontCommon;
    }

    /**
     * @param mixed $fontCommonBold
     */
    public function setFontCommonBold($fontCommonBold)
    {
        $this->_fontCommonBold = $fontCommonBold;
    }

    /**
     * @param mixed $template
     */
    public function setTemplate($template)
    {
        $this->_template = $template;
    }

    /**
     * @param mixed $priceFontSize
     */
    public function setPriceFontSize($priceFontSize)
    {
        $this->_priceFontSize = $priceFontSize;
    }

    /**
     * @param mixed $commonFontSize
     */
    public function setCommonFontSize($commonFontSize)
    {
        $this->_commonFontSize = $commonFontSize;
    }

    /**
     * @param int $imgWidth
     */
    public function setImgWidth(int $imgWidth)
    {
        $this->_imgWidth = $imgWidth;
    }

    /**
     * @param int $imgHeight
     */
    public function setImgHeight(int $imgHeight)
    {
        $this->_imgHeight = $imgHeight;
    }

    /**
     * @param array $price
     */
    public function setPrice(array $price)
    {
        $this->_price = $price;
    }

    /**
     * @param int $padding
     */
    public function setPadding(int $padding)
    {
        $this->_padding = $padding;
    }

    /**
     * @param int $angle
     */
    public function setAngle(int $angle)
    {
        $this->_angle = $angle;
    }

    /**
     * @return mixed
     */
    public function getPosition()
    {
        return $this->_position;
    }

    private function _createPCListPriceImg()
    {
        $image = null;

        $prices = $this->_price;

        if (is_array($prices) && count($prices) > 0) {
            $bigImgWidth  = $this->_imgWidth;
            $bigImgHeight = $this->_imgHeight;

            try {
                // 创建透明图片
                $image = imagecreatetruecolor($bigImgWidth, $bigImgHeight);
                imagesavealpha($image, true);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);

                $red_text_color   = imagecolorallocate($image, 228, 57, 60);
                $black_text_color = imagecolorallocate($image, 51, 51, 51);

                foreach ($prices as $item) {
                    $price                    = $item['price'];
                    $unitPrice                = $item['unitPrice'];
                    $priceText                = $item["priceText"];
                    $unitPriceText            = $item["unitPriceText"];
                    $position                 = $item['position'];
                    $priceTextWidthHeight     = $item['priceTextWidthHeight'];
                    $unitPriceWidthHeight     = $item['unitPriceWidthHeight'];
                    $unitPriceTextWidthHeight = $item['unitPriceTextWidthHeight'];

                    $price_x = $position[0][0];
                    $price_y = $this->_imgHeight - $unitPriceWidthHeight[1] - $this->_padding * 3;
                    imagettftext($image, $this->_priceFontSize, $this->_angle, $price_x, $price_y, $red_text_color, $this->_fontNumberBold, $price);

                    $priceText_x = $position[1][0] - $priceTextWidthHeight[0] - 5;
                    $priceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $priceText_x, $priceText_y, $red_text_color, $this->_fontCommonBold, $priceText);

                    $unitPrice_x = $position[1][0] - ($unitPriceWidthHeight[0] + $unitPriceTextWidthHeight[0]) - $this->_padding;
                    $unitPrice_y = $this->_imgHeight - $this->_padding;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPrice_x, $unitPrice_y, $black_text_color, $this->_fontNumber, $unitPrice);

                    $unitPriceText_x = $position[1][0] - $unitPriceTextWidthHeight[0] - ($this->_padding / 2) - 1;
                    $unitPriceText_y = $unitPrice_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPriceText_x, $unitPriceText_y, $black_text_color, $this->_fontCommon, $unitPriceText);
                }
            } catch (\Exception $e) {
                $image = null;
            }
        }

        return $image;
    }

    private function _createPCDetailPriceImg()
    {
        $image = null;

        $prices = $this->_price;

        if (is_array($prices) && count($prices) > 0) {
            $bigImgWidth  = $this->_imgWidth * count($prices);
            $bigImgHeight = $this->_imgHeight;

            try {
                // 创建透明图片
                $image = imagecreatetruecolor($bigImgWidth, $bigImgHeight);
                imagesavealpha($image, true);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);

                $red_text_color = imagecolorallocate($image, 228, 57, 60);

                foreach ($prices as $i => $item) {
                    $price                = $item['price'];
                    $unitPrice            = $item['unitPrice'];
                    $priceText            = $item["priceText"];
                    $unitPriceText        = $item["unitPriceText"];
                    $priceWidthHeight     = $item['priceWidthHeight'];
                    $priceTextWidthHeight = $item['priceTextWidthHeight'];
                    $unitPriceWidthHeight = $item['unitPriceWidthHeight'];

                    $price_x = 0;
                    $price_y = $this->_imgHeight - $this->_padding;
                    imagettftext($image, $this->_priceFontSize, $this->_angle, $price_x, $price_y, $red_text_color, $this->_fontNumberBold, $price);

                    $priceText_x = $price_x + $priceWidthHeight[0] + $this->_padding;
                    $priceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $priceText_x, $priceText_y, $red_text_color, $this->_fontCommonBold, $priceText);

                    $unitPrice_x = $priceText_x + $priceTextWidthHeight[0] + $this->_padding;
                    $unitPrice_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPrice_x, $unitPrice_y, $red_text_color, $this->_fontNumber, $unitPrice);

                    $unitPriceText_x = $unitPrice_x + $unitPriceWidthHeight[0];
                    $unitPriceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPriceText_x, $unitPriceText_y, $red_text_color, $this->_fontCommon, $unitPriceText);
                }
            } catch (\Exception $e) {
                $image = null;
            }
        }

        return $image;
    }

    private function _createMListPriceImg()
    {
        $image = null;

        $prices = $this->_price;

        if (is_array($prices) && count($prices) > 0) {
            $bigImgWidth  = $this->_imgWidth;
            $bigImgHeight = $this->_imgHeight;

            try {
                // 创建透明图片
                $image = imagecreatetruecolor($bigImgWidth, $bigImgHeight);
                imagesavealpha($image, true);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);

                $red_text_color = imagecolorallocate($image, 250, 87, 65);

                foreach ($prices as $i => $item) {
                    $price                = $item['price'];
                    $priceText            = $item["priceText"];
                    $position             = $item['position'];
                    $priceTextWidthHeight = $item['priceTextWidthHeight'];

                    $price_x = $position[0][0];
                    $price_y = $this->_imgHeight - $this->_padding;
                    imagettftext($image, $this->_priceFontSize, $this->_angle, $price_x, $price_y, $red_text_color, $this->_fontNumberBold, $price);

                    $priceText_x = $position[1][0] - $priceTextWidthHeight[0] - $this->_padding;
                    $priceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $priceText_x, $priceText_y, $red_text_color, $this->_fontCommonBold, $priceText);
                }
            } catch (\Exception $e) {
                $image = null;
            }
        }

        return $image;
    }

    private function _createMDetailPriceImg()
    {
        $image = null;

        $prices = $this->_price;

        if (is_array($prices) && count($prices) > 0) {
            $bigImgWidth  = $this->_imgWidth;
            $bigImgHeight = $this->_imgHeight;

            try {
                // 创建透明图片
                $image = imagecreatetruecolor($bigImgWidth, $bigImgHeight);
                imagesavealpha($image, true);
                $color = imagecolorallocatealpha($image, 0, 0, 0, 127);
                imagefill($image, 0, 0, $color);

                $red_text_color = imagecolorallocate($image, 250, 87, 65);

                foreach ($prices as $i => $item) {
                    $price                = $item['price'];
                    $unitPrice            = $item['unitPrice'];
                    $priceText            = $item["priceText"];
                    $unitPriceText        = $item["unitPriceText"];
                    $priceWidthHeight     = $item['priceWidthHeight'];
                    $priceTextWidthHeight = $item['priceTextWidthHeight'];
                    $unitPriceWidthHeight = $item['unitPriceWidthHeight'];

                    $price_x = 0;
                    $price_y = $this->_imgHeight - $this->_padding;
                    imagettftext($image, $this->_priceFontSize, $this->_angle, $price_x, $price_y, $red_text_color, $this->_fontCommonBold, $price);

                    $priceText_x = $price_x + $priceWidthHeight[0] + $this->_padding / 2;
                    $priceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $priceText_x, $priceText_y, $red_text_color, $this->_fontCommonBold, $priceText);

                    $unitPrice_x = $priceText_x + $priceTextWidthHeight[0] + $this->_padding;
                    $unitPrice_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPrice_x, $unitPrice_y, $red_text_color, $this->_fontCommonBold, $unitPrice);

                    $unitPriceText_x = $unitPrice_x + $unitPriceWidthHeight[0] + $this->_padding / 2;
                    $unitPriceText_y = $price_y;
                    imagettftext($image, $this->_commonFontSize, $this->_angle, $unitPriceText_x, $unitPriceText_y, $red_text_color, $this->_fontCommonBold, $unitPriceText);
                }
            } catch (\Exception $e) {
                $image = null;
            }
        }

        return $image;
    }

    private function _getFontWidthHeight($size, $font, $string)
    {
        if (file_exists($font)) {
            $box    = imagettfbbox($size, $this->_angle, $font, $string);
            $width  = abs($box[2]) - abs($box[0]);
            $height = abs($box[7]) - abs($box[1]);

            return [$width, $height];
        }

        return null;
    }
}