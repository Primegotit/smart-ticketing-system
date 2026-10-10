<?php
namespace app\helpers;

require_once dirname(__DIR__) . '/lib/qrcode.php';

/**
 * Generates QR codes locally (no external web service, no GD extension needed).
 * The QR content for a ticket is ONLY the ticket's qr_code_hash, which keeps the
 * code small/low-density so phone cameras read it reliably.
 */
class QrImage {

    /**
     * @return array{size:int, rows:array<int,array<int,bool>>}
     */
    public static function matrix($text, $level = 'Q') {
        $levels = [
            'L' => QR_ERROR_CORRECT_LEVEL_L,
            'M' => QR_ERROR_CORRECT_LEVEL_M,
            'Q' => QR_ERROR_CORRECT_LEVEL_Q,
            'H' => QR_ERROR_CORRECT_LEVEL_H,
        ];
        $qr = \QRCode::getMinimumQRCode((string)$text, $levels[$level] ?? QR_ERROR_CORRECT_LEVEL_Q);
        $n = $qr->getModuleCount();
        $rows = [];
        for ($r = 0; $r < $n; $r++) {
            $row = [];
            for ($c = 0; $c < $n; $c++) {
                $row[] = (bool)$qr->isDark($r, $c);
            }
            $rows[] = $row;
        }
        return ['size' => $n, 'rows' => $rows];
    }

    /** PNG (8-bit grayscale) binary string. */
    public static function png($text, $scale = 8, $quiet = 4, $level = 'Q') {
        $m = self::matrix($text, $level);
        $n = $m['size'];
        $dim = ($n + 2 * $quiet) * $scale;

        $raw = '';
        $blankRow = "\x00" . str_repeat("\xFF", $dim);
        for ($i = 0; $i < $quiet * $scale; $i++) {
            $raw .= $blankRow;
        }
        for ($r = 0; $r < $n; $r++) {
            $line = str_repeat("\xFF", $quiet * $scale);
            for ($c = 0; $c < $n; $c++) {
                $line .= str_repeat($m['rows'][$r][$c] ? "\x00" : "\xFF", $scale);
            }
            $line .= str_repeat("\xFF", $quiet * $scale);
            $line = "\x00" . $line;
            for ($i = 0; $i < $scale; $i++) {
                $raw .= $line;
            }
        }
        for ($i = 0; $i < $quiet * $scale; $i++) {
            $raw .= $blankRow;
        }

        $chunk = function ($type, $data) {
            return pack('N', strlen($data)) . $type . $data
                . pack('N', crc32($type . $data) & 0xFFFFFFFF);
        };

        return "\x89PNG\r\n\x1a\n"
            . $chunk('IHDR', pack('NNCCCCC', $dim, $dim, 8, 0, 0, 0, 0))
            . $chunk('IDAT', gzcompress($raw, 6))
            . $chunk('IEND', '');
    }

    /** SVG markup (used as a fallback). */
    public static function svg($text, $quiet = 4, $level = 'Q') {
        $m = self::matrix($text, $level);
        $n = $m['size'];
        $dim = $n + 2 * $quiet;
        $path = '';
        for ($r = 0; $r < $n; $r++) {
            $c = 0;
            while ($c < $n) {
                if ($m['rows'][$r][$c]) {
                    $start = $c;
                    while ($c < $n && $m['rows'][$r][$c]) {
                        $c++;
                    }
                    $path .= 'M' . ($start + $quiet) . ' ' . ($r + $quiet) . 'h' . ($c - $start) . 'v1h-' . ($c - $start) . 'z';
                } else {
                    $c++;
                }
            }
        }
        return '<?xml version="1.0" encoding="UTF-8"?>'
            . '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ' . $dim . ' ' . $dim . '" shape-rendering="crispEdges">'
            . '<rect width="100%" height="100%" fill="#fff"/><path d="' . $path . '" fill="#000"/></svg>';
    }
}
