<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use OpenSpout\Common\Entity\Cell\FormulaCell;
use OpenSpout\Reader\XLSX\Options;
use OpenSpout\Reader\XLSX\Reader;
use OpenSpout\Reader\XLSX\Sheet;
use RuntimeException;

class PratinjauExcel
{
    private const BARIS_PER_HALAMAN = 50;
    private const KOLOM_PER_HALAMAN = 26;

    public static function baca(string $path, ?int $lembar, int $halaman, int $kolom): array
    {
        $opsi = new Options();
        $opsi->SHOULD_FORMAT_DATES = true;
        $opsi->SHOULD_PRESERVE_EMPTY_ROWS = true;
        $reader = new Reader($opsi);

        $sementara = tempnam(sys_get_temp_dir(), 'lms-excel-');

        if ($sementara === false) {
            throw new RuntimeException('File sementara untuk pratinjau Excel gagal dibuat.');
        }

        try {
            self::salinKeLokal($path, $sementara);
            $reader->open($sementara);
            $daftarLembar = [];
            $pilihan = null;

            foreach ($reader->getSheetIterator() as $sheet) {
                if (! $sheet->isVisible()) {
                    continue;
                }

                $indeks = $sheet->getIndex();
                $daftarLembar[$indeks] = $sheet->getName();

                if ($pilihan === null && ($lembar === null || $lembar === $indeks)) {
                    $pilihan = self::bacaHalaman($sheet, $halaman, $kolom);
                }
            }

            return ['lembar' => $daftarLembar, 'pilihan' => $pilihan];
        } finally {
            try {
                $reader->close();
            } finally {
                unlink($sementara);
            }
        }
    }

    private static function bacaHalaman(Sheet $sheet, int $halaman, int $kolom): array
    {
        $awalBaris = ($halaman - 1) * self::BARIS_PER_HALAMAN;
        $awalKolom = ($kolom - 1) * self::KOLOM_PER_HALAMAN;
        $baris = [];
        $jumlahKolom = 0;
        $barisLain = false;
        $kolomLain = false;

        foreach ($sheet->getRowIterator() as $nomor => $row) {
            if ($nomor <= $awalBaris) {
                continue;
            }

            if (count($baris) === self::BARIS_PER_HALAMAN) {
                $barisLain = true;
                break;
            }

            $sel = $row->getCells();
            $kolomLain = $kolomLain || count($sel) > $awalKolom + self::KOLOM_PER_HALAMAN;
            $isi = [];

            foreach (array_slice($sel, $awalKolom, self::KOLOM_PER_HALAMAN) as $cell) {
                $nilai = $cell instanceof FormulaCell ? $cell->getComputedValue() : $cell->getValue();
                $isi[] = match (true) {
                    $nilai instanceof \DateTimeInterface => $nilai->format('Y-m-d H:i:s'),
                    $nilai instanceof \DateInterval => $nilai->format('%H:%I:%S'),
                    is_bool($nilai) => $nilai ? 'TRUE' : 'FALSE',
                    default => (string) $nilai,
                };
            }

            $baris[$nomor] = $isi;
            $jumlahKolom = max($jumlahKolom, count($isi));
        }

        return [
            'indeks' => $sheet->getIndex(),
            'baris' => $baris,
            'jumlahKolom' => $jumlahKolom,
            'awalKolom' => $awalKolom,
            'halaman' => $halaman,
            'kolom' => $kolom,
            'barisLain' => $barisLain,
            'kolomLain' => $kolomLain,
        ];
    }

    private static function salinKeLokal(string $path, string $sementara): void
    {
        $stream = Storage::readStream($path);

        if (! is_resource($stream)) {
            throw new RuntimeException('File Excel tidak dapat dibaca dari penyimpanan.');
        }

        try {
            $tujuan = fopen($sementara, 'wb');

            if ($tujuan === false) {
                throw new RuntimeException('File sementara Excel tidak dapat dibuka.');
            }

            try {
                if (stream_copy_to_stream($stream, $tujuan) === false) {
                    throw new RuntimeException('File Excel gagal disalin untuk pratinjau.');
                }
            } finally {
                fclose($tujuan);
            }
        } finally {
            fclose($stream);
        }
    }
}
