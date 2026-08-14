<?php

/**
 * app/helpers.php
 *
 * Helper global untuk konversi angka menjadi kata (terbilang) dalam Bahasa Indonesia.
 * Dipakai oleh view resources/views/pembelian/pdf.blade.php.
 *
 * Cara pasang di Laravel 12:
 * 1. Simpan file ini sebagai app/helpers.php
 * 2. Daftarkan di composer.json:
 *
 *      "autoload": {
 *          "files": [
 *              "app/helpers.php"
 *          ]
 *      }
 *
 * 3. Jalankan: composer dump-autoload
 */

if (!function_exists('terbilang')) {
  /**
   * Konversi angka menjadi kata dalam Bahasa Indonesia (bilangan bulat).
   */
  function terbilang(int $angka): string
  {
    $angka = abs($angka);
    $huruf = [
      '',
      'satu',
      'dua',
      'tiga',
      'empat',
      'lima',
      'enam',
      'tujuh',
      'delapan',
      'sembilan',
      'sepuluh',
      'sebelas',
    ];

    if ($angka < 12) {
      return $huruf[$angka];
    }

    if ($angka < 20) {
      return terbilang($angka - 10) . ' belas';
    }

    if ($angka < 100) {
      $sisa = $angka % 10;
      return trim(terbilang((int) ($angka / 10)) . ' puluh ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    if ($angka < 200) {
      return trim('seratus ' . terbilang($angka - 100));
    }

    if ($angka < 1000) {
      $sisa = $angka % 100;
      return trim(terbilang((int) ($angka / 100)) . ' ratus ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    if ($angka < 2000) {
      $sisa = $angka - 1000;
      return trim('seribu ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    if ($angka < 1000000) {
      $sisa = $angka % 1000;
      return trim(terbilang((int) ($angka / 1000)) . ' ribu ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    if ($angka < 1000000000) {
      $sisa = $angka % 1000000;
      return trim(terbilang((int) ($angka / 1000000)) . ' juta ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    if ($angka < 1000000000000) {
      $sisa = $angka % 1000000000;
      return trim(terbilang((int) ($angka / 1000000000)) . ' miliar ' . ($sisa > 0 ? terbilang($sisa) : ''));
    }

    $sisa = $angka % 1000000000000;
    return trim(terbilang((int) ($angka / 1000000000000)) . ' triliun ' . ($sisa > 0 ? terbilang($sisa) : ''));
  }
}

if (!function_exists('terbilang_rupiah')) {
  /**
   * Konversi angka (boleh desimal) menjadi kata terbilang dengan huruf kapital
   * di awal setiap kata, meniru gaya penulisan pada dokumen resmi.
   * Bagian desimal dibulatkan ke bawah (rupiah bulat).
   */
  function terbilang_rupiah(float $angka): string
  {
    $bulat = (int) floor($angka);

    if ($bulat === 0) {
      return 'Nol';
    }

    $kata = terbilang($bulat);
    $kata = preg_replace('/\s+/', ' ', trim($kata));

    return ucwords($kata);
  }
}
