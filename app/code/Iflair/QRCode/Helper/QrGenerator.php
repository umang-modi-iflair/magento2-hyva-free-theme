<?php
namespace Iflair\QRCode\Helper;

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;

class QrGenerator
{
    public function generate(string $text): string
    {
        $options = new QROptions([
            'version'      => 5,
            'outputType'   => QRCode::OUTPUT_IMAGE_PNG,
            'eccLevel'     => QRCode::ECC_L,
            'scale'        => 10,
            'imageBase64'  => false, 
        ]);

        return (new QRCode($options))->render($text);
    }
}