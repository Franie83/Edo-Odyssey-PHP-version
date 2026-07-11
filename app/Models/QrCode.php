<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use chillerlan\QRCode\QRCode as QRCodeGenerator;
use chillerlan\QRCode\QROptions;

class QrCode extends Model
{
    protected $table = 'qr_codes';

    protected $fillable = ['entity_type', 'entity_id', 'code', 'image_path', 'scan_count', 'last_scanned_at'];
    protected $casts    = ['last_scanned_at' => 'datetime'];

    public static function generateForEntity(string $type, int $id): self
    {
        $code = strtoupper(\Illuminate\Support\Str::random(10));
        $url  = route('qr.scan', $code);

        $qr = self::updateOrCreate(
            ['entity_type' => $type, 'entity_id' => $id],
            ['code' => $code]
        );

        try {
            $dir = storage_path("app/public/qr_codes");
            if (!is_dir($dir)) mkdir($dir, 0755, true);
            $path = "qr_codes/{$code}.svg";

            // ✅ v5 constants (no detection)
            $options = new QROptions([
                'version'       => 5,
                'outputType'    => QRCodeGenerator::OUTPUT_MARKUP_SVG,
                'eccLevel'      => QRCodeGenerator::ECC_L,
                'imageBase64'   => false,
                'addQuietzone'  => true,
                'quietzoneSize' => 2,
            ]);

            $qrCode = new QRCodeGenerator($options);
            $svg = $qrCode->render($url);

            $svg = trim($svg);
            if (strpos($svg, '<') !== 0) {
                \Log::warning('QR SVG does not start with <: ' . substr($svg, 0, 50));
                if (preg_match('/<svg[^>]*>.*<\/svg>/s', $svg, $matches)) {
                    $svg = $matches[0];
                }
            }

            file_put_contents(storage_path("app/public/{$path}"), $svg);
            $qr->update(['image_path' => $path]);

        } catch (\Exception $e) {
            \Log::error('QR generation failed: ' . $e->getMessage());
        }

        return $qr;
    }
}