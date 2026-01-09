<?php
namespace Iflair\BarCode\Helper;

use Picqer\Barcode\BarcodeGeneratorPNG;

class qrGenerator
{
    public function generate($text)
    {
        $generator = new BarcodeGeneratorPNG();
        return $generator->getBarcode(
            $text,
            $generator::TYPE_CODE_128
        );
    }
}
