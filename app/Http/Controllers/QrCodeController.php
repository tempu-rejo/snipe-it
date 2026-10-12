<?php

namespace App\Http\Controllers;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Response;
use Illuminate\Http\Request;

class QrCodeController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $data = $request->string('data')->toString();

        abort_if($data === '', 422, 'QR data is required.');

        $renderer = new ImageRenderer(
            new RendererStyle(256),
            new SvgImageBackEnd()
        );

        return response(
            (new Writer($renderer))->writeString($data),
            200,
            ['Content-Type' => 'image/svg+xml']
        );
    }
}
