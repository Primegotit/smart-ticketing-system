<?php
namespace app\helpers;

/**
 * Minimal single-page PDF writer used to produce the downloadable ticket.
 * No external libraries: uses the built-in Helvetica / Courier fonts and draws
 * the QR code as vector squares. The ticket's qr_code_hash is printed on the PDF
 * so the attendee can use it for manual entry if scanning fails.
 */
class TicketPdf {

    // Helvetica glyph widths (1/1000 em) for WinAnsi codes 32..255
    private static $helv = [
        278, 278, 355, 556, 556, 889, 667, 191, 333, 333, 389, 584, 278, 333, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 278, 278, 584, 584, 584, 556,
        1015, 667, 667, 722, 722, 667, 611, 778, 722, 278, 500, 667, 556, 833, 722, 778,
        667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 278, 278, 278, 469, 556,
        333, 556, 556, 500, 556, 556, 278, 556, 556, 222, 222, 500, 222, 833, 556, 556,
        556, 556, 333, 500, 278, 556, 500, 722, 500, 500, 500, 334, 260, 334, 584, 761,
        556, 278, 222, 556, 333, 1000, 556, 556, 333, 1000, 667, 333, 1000, 278, 611, 278,
        278, 222, 222, 333, 333, 350, 556, 1000, 333, 1000, 500, 333, 944, 278, 500, 667,
        278, 333, 556, 556, 556, 556, 260, 556, 333, 737, 370, 556, 584, 333, 737, 333,
        400, 584, 333, 333, 333, 556, 537, 278, 333, 333, 365, 556, 834, 834, 834, 611,
        667, 667, 667, 667, 667, 667, 1000, 722, 667, 667, 667, 667, 278, 278, 278, 278,
        722, 722, 778, 778, 778, 778, 778, 584, 778, 722, 722, 722, 722, 667, 667, 611,
        556, 556, 556, 556, 556, 556, 889, 500, 556, 556, 556, 556, 278, 278, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 584, 611, 556, 556, 556, 556, 500, 556, 500
    ];
    private static $helvBold = [
        278, 333, 474, 556, 556, 889, 722, 238, 333, 333, 389, 584, 278, 333, 278, 278,
        556, 556, 556, 556, 556, 556, 556, 556, 556, 556, 333, 333, 584, 584, 584, 611,
        975, 722, 722, 722, 722, 667, 611, 778, 722, 278, 556, 722, 611, 833, 722, 778,
        667, 778, 722, 667, 611, 722, 667, 944, 667, 667, 611, 333, 278, 333, 584, 556,
        333, 556, 611, 556, 611, 556, 333, 611, 611, 278, 278, 556, 278, 889, 611, 611,
        611, 611, 389, 556, 333, 611, 556, 778, 556, 556, 500, 389, 280, 389, 584, 761,
        556, 278, 278, 556, 500, 1000, 556, 556, 333, 1000, 667, 333, 1000, 278, 611, 278,
        278, 278, 278, 500, 500, 350, 556, 1000, 333, 1000, 556, 333, 944, 278, 500, 667,
        278, 333, 556, 556, 556, 556, 280, 556, 333, 737, 370, 556, 584, 333, 737, 333,
        400, 584, 333, 333, 333, 611, 556, 278, 333, 333, 365, 556, 834, 834, 834, 611,
        722, 722, 722, 722, 722, 722, 1000, 722, 667, 667, 667, 667, 278, 278, 278, 278,
        722, 722, 778, 778, 778, 778, 778, 584, 778, 722, 722, 722, 722, 667, 667, 611,
        556, 556, 556, 556, 556, 556, 889, 556, 556, 556, 556, 556, 278, 278, 278, 278,
        611, 611, 611, 611, 611, 611, 611, 584, 611, 611, 611, 611, 611, 556, 611, 556
    ];

    private $ops = '';

    // ---------- public API ----------

    /**
     * @param array $t ticket row: id, event_title, venue, date_time, first_name, last_name,
     *                 email, qr_code_hash, is_scanned, created_at
     */
    public static function ticket(array $t, $siteName = 'Smart Ticketing') {
        $pdf = new self();
        return $pdf->buildTicket($t, $siteName);
    }

    // ---------- ticket layout (A4 portrait, 595 x 842 pt) ----------

    private function buildTicket(array $t, $siteName) {
        $W = 595; $H = 842;
        $left = 60; $right = $W - 60; $cx = $W / 2;

        // header band
        $this->rect(0, $H - 90, $W, 90, [0.05, 0.43, 0.99]);
        $this->text($siteName, $left, $H - 52, 'B', 22, [1, 1, 1]);
        $this->text('EVENT TICKET', $right - $this->width('EVENT TICKET', 'B', 14), $H - 50, 'B', 14, [1, 1, 1]);

        // event title (wrapped)
        $y = $H - 140;
        foreach (array_slice($this->wrap((string)$t['event_title'], 'B', 24, $right - $left), 0, 3) as $line) {
            $this->text($line, $left, $y, 'B', 24, [0.1, 0.1, 0.1]);
            $y -= 30;
        }
        $y -= 6;

        $ts = strtotime($t['date_time']);
        $rows = [
            ['VENUE', (string)$t['venue']],
            ['DATE', $ts ? date('l, F j, Y', $ts) : ''],
            ['TIME', $ts ? date('g:i A', $ts) : ''],
            ['ATTENDEE', trim($t['first_name'] . ' ' . $t['last_name'])],
            ['EMAIL', (string)($t['email'] ?? '')],
            ['TICKET NO.', '#' . $t['id']],
        ];
        foreach ($rows as $r) {
            $this->text($r[0], $left, $y, 'B', 9, [0.45, 0.45, 0.45]);
            $val = $this->wrap($r[1], 'N', 13, $right - $left - 90);
            $this->text($val[0] ?? '', $left + 90, $y, 'N', 13, [0.1, 0.1, 0.1]);
            $y -= 22;
        }

        // divider
        $y -= 6;
        $this->rect($left, $y, $right - $left, 1, [0.8, 0.8, 0.8]);
        $y -= 30;

        // QR code (encodes ONLY the hash)
        $qrSize = 200;
        $qrX = $cx - $qrSize / 2;
        $qrY = $y - $qrSize;
        $this->qr((string)$t['qr_code_hash'], $qrX, $qrY, $qrSize);
        $y = $qrY - 24;

        $this->centerText('Scan this QR code at the event entrance', $cx, $y, 'N', 12, [0.25, 0.25, 0.25]);
        $y -= 30;

        // hash box
        $this->rect($left, $y - 62, $right - $left, 76, [0.96, 0.97, 1.0]);
        $this->centerText('TICKET CODE  -  type this in if the QR code will not scan', $cx, $y, 'B', 10, [0.35, 0.35, 0.35]);
        $hash = (string)$t['qr_code_hash'];
        $size = 17;
        while ($size > 9 && $this->monoWidth($hash, $size) > ($right - $left - 24)) {
            $size--;
        }
        $this->centerText($hash, $cx, $y - 34, 'M', $size, [0.05, 0.05, 0.05]);

        // status + footer
        $y -= 92;
        $status = !empty($t['is_scanned']) ? 'STATUS: ALREADY USED' : 'STATUS: VALID FOR ENTRY';
        $this->centerText($status, $cx, $y, 'B', 11, !empty($t['is_scanned']) ? [0.75, 0.1, 0.1] : [0.1, 0.55, 0.2]);

        $this->centerText('Each ticket can be used once. Do not share this ticket or its code.', $cx, 60, 'N', 9, [0.5, 0.5, 0.5]);
        if (!empty($t['created_at'])) {
            $this->centerText('Issued ' . date('M j, Y g:i A', strtotime($t['created_at'])), $cx, 46, 'N', 9, [0.5, 0.5, 0.5]);
        }

        return $this->output($W, $H);
    }

    // ---------- drawing primitives ----------

    private function n($v) { return rtrim(rtrim(number_format($v, 3, '.', ''), '0'), '.') ?: '0'; }

    private function color(array $c) { return $this->n($c[0]) . ' ' . $this->n($c[1]) . ' ' . $this->n($c[2]); }

    private function rect($x, $y, $w, $h, array $rgb) {
        $this->ops .= $this->color($rgb) . " rg\n" . $this->n($x) . ' ' . $this->n($y) . ' ' . $this->n($w) . ' ' . $this->n($h) . " re f\n";
    }

    private function text($s, $x, $y, $font, $size, array $rgb) {
        $name = $font === 'B' ? 'F2' : ($font === 'M' ? 'F3' : 'F1');
        $this->ops .= 'BT /' . $name . ' ' . $this->n($size) . ' Tf ' . $this->color($rgb) . ' rg '
            . $this->n($x) . ' ' . $this->n($y) . ' Td (' . $this->esc($this->enc($s)) . ") Tj ET\n";
    }

    private function centerText($s, $cx, $y, $font, $size, array $rgb) {
        $w = $font === 'M' ? $this->monoWidth($s, $size) : $this->width($s, $font, $size);
        $this->text($s, $cx - $w / 2, $y, $font, $size, $rgb);
    }

    private function qr($data, $x, $y, $size) {
        $m = QrImage::matrix($data, 'Q');
        $quiet = 4;
        $n = $m['size'];
        $cell = $size / ($n + 2 * $quiet);
        $this->rect($x, $y, $size, $size, [1, 1, 1]);
        $this->ops .= "0 0 0 rg\n";
        for ($r = 0; $r < $n; $r++) {
            $c = 0;
            while ($c < $n) {
                if ($m['rows'][$r][$c]) {
                    $start = $c;
                    while ($c < $n && $m['rows'][$r][$c]) { $c++; }
                    $px = $x + ($start + $quiet) * $cell;
                    $py = $y + $size - ($r + $quiet + 1) * $cell;
                    // tiny overlap avoids hairline gaps in some viewers
                    $this->ops .= $this->n($px) . ' ' . $this->n($py) . ' ' . $this->n(($c - $start) * $cell + 0.05) . ' ' . $this->n($cell + 0.05) . " re f\n";
                } else {
                    $c++;
                }
            }
        }
    }

    // ---------- text helpers ----------

    private function enc($s) {
        $s = (string)$s;
        if (function_exists('iconv')) {
            $r = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $s);
            if ($r !== false) { return $r; }
        }
        if (function_exists('mb_convert_encoding')) {
            return mb_convert_encoding($s, 'Windows-1252', 'UTF-8');
        }
        return preg_replace('/[^\x20-\x7E]/', '?', $s);
    }

    private function esc($s) { return str_replace(['\\', '(', ')', "\r", "\n"], ['\\\\', '\\(', '\\)', '', ' '], $s); }

    private function width($s, $font, $size) {
        $tbl = $font === 'B' ? self::$helvBold : self::$helv;
        $s = $this->enc($s);
        $w = 0;
        for ($i = 0, $l = strlen($s); $i < $l; $i++) {
            $code = ord($s[$i]);
            $w += $tbl[$code - 32] ?? 556;
        }
        return $w * $size / 1000;
    }

    private function monoWidth($s, $size) { return strlen($this->enc($s)) * 0.6 * $size; }

    private function wrap($s, $font, $size, $maxWidth) {
        $words = preg_split('/\s+/', trim($s));
        $lines = [];
        $cur = '';
        foreach ($words as $word) {
            $try = $cur === '' ? $word : $cur . ' ' . $word;
            if ($this->width($try, $font, $size) <= $maxWidth) {
                $cur = $try;
            } else {
                if ($cur !== '') { $lines[] = $cur; }
                $cur = $word;
                // hard-break a single very long word
                while ($this->width($cur, $font, $size) > $maxWidth && strlen($cur) > 1) {
                    $cut = strlen($cur) - 1;
                    while ($cut > 1 && $this->width(substr($cur, 0, $cut), $font, $size) > $maxWidth) { $cut--; }
                    $lines[] = substr($cur, 0, $cut);
                    $cur = substr($cur, $cut);
                }
            }
        }
        if ($cur !== '') { $lines[] = $cur; }
        return $lines ?: [''];
    }

    // ---------- PDF serialisation ----------

    private function output($W, $H) {
        $objs = [];
        $objs[1] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objs[2] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objs[3] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 ' . $W . ' ' . $H . '] /Resources << /Font << /F1 5 0 R /F2 6 0 R /F3 7 0 R >> >> /Contents 4 0 R >>';
        $objs[4] = '<< /Length ' . strlen($this->ops) . " >>\nstream\n" . $this->ops . "endstream";
        $objs[5] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>';
        $objs[6] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold /Encoding /WinAnsiEncoding >>';
        $objs[7] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold /Encoding /WinAnsiEncoding >>';

        $out = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        foreach ($objs as $i => $body) {
            $offsets[$i] = strlen($out);
            $out .= $i . " 0 obj\n" . $body . "\nendobj\n";
        }
        $xref = strlen($out);
        $count = count($objs) + 1;
        $out .= "xref\n0 $count\n0000000000 65535 f \n";
        foreach ($offsets as $off) {
            $out .= sprintf("%010d 00000 n \n", $off);
        }
        $out .= "trailer\n<< /Size $count /Root 1 0 R >>\nstartxref\n$xref\n%%EOF";
        return $out;
    }
}
