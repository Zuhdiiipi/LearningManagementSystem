<?php

namespace App\Support;
use Carbon\Carbon;
class Waktu
{
    // Format singkat seperti di desain: "Hari ini, 10:30", "Kemarin, 01:20", "Sabtu, 23:59", "1 Oktober 2025"
    public static function singkat(?Carbon $waktu, bool $pakaiJam = true): string
    {
        if (! $waktu) {
            return '-';
        }
        $jam = $pakaiJam ? ', ' . $waktu->format('H : i') : '';

        if ($waktu->isToday()) {
            return 'Hari ini' . $jam;
        }
        if ($waktu->isYesterday()) {
            return 'Kemarin' . $jam;
        }
        if ($waktu->isTomorrow()) {
            return 'Besok' . $jam;
        }
        // Dalam seminggu ke depan cukup nama harinya
        if ($waktu->isFuture() && $waktu->diffInDays(now(), true) < 7) {
            return $waktu->translatedFormat('l') . $jam;
        }

        return $waktu->translatedFormat('j F Y');
    }

    // Inisial nama untuk foto profil default, contoh: "Ahmad Suardi" -> "AS"
    public static function inisial(string $nama): string
    {
        return collect(explode(' ', trim($nama)))->filter()->take(2)
            ->map(fn ($k) => mb_strtoupper(mb_substr($k, 0, 1)))->implode('');
    }
}
