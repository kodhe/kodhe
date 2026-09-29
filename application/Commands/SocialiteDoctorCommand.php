<?php

declare(strict_types=1);

namespace App\Commands;

use Kodhe\Framework\Console\Command;

/**
 * Socialite Doctor — diagnostic CLI untuk stack social login.
 *
 * Usage:
 *   php bin/console socialite:doctor            # laporan lengkap (dengan ping)
 *   php bin/console socialite:doctor --json     # output machine-readable
 *   php bin/console socialite:doctor --no-ping  # lewati cek konektivitas host
 *
 * Merender hasil helper application/helpers/socialite_doctor.php: status
 * .env, kredensial per provider, endpoint OAuth nyata (authorize/token/
 * userinfo), routes, dan prasyarat runtime. Exit code 0 = sehat, 2 = ada FAIL.
 */
class SocialiteDoctorCommand extends Command
{
    protected string $name = 'socialite:doctor';
    protected string $description = 'Diagnosa konfigurasi social login (.env, kredensial provider, endpoint OAuth, routes, runtime)';
    protected array $usage = [
        'socialite:doctor',
        'socialite:doctor --json',
        'socialite:doctor --no-ping',
    ];

    public function handle(): int
    {
        $json   = (bool) $this->option('json', false);
        $noPing = (bool) $this->option('no-ping', false);

        $helper = APPPATH . 'helpers/socialite_doctor.php';

        if (!is_file($helper)) {
            $this->error("Helper tidak ditemukan: {$helper}");
            return 1;
        }

        require_once $helper;

        if (!function_exists('socialite_doctor_report')) {
            $this->error('Fungsi socialite_doctor_report() tidak tersedia.');
            return 1;
        }

        try {
            $report = socialite_doctor_report();
        } catch (\Throwable $e) {
            $this->error('Gagal membangun laporan: ' . $e->getMessage());
            return 1;
        }

        if ($noPing) {
            foreach ($report['providers'] as &$p) {
                unset($p['reachable'], $p['http_status'], $p['http_error']);
            }
            unset($p);
            // Ganti status fail yang murni disebabkan unreachable-host.
            foreach ($report['checks'] as &$c) {
                if ($c['group'] === 'providers' && $c['status'] === 'fail'
                    && str_contains((string) ($c['hint'] ?? ''), 'tidak terjangkau')) {
                    $c['status'] = 'info';
                    $c['hint']   = 'Ping dilewati (--no-ping).';
                }
            }
            unset($c);
        }

        if ($json) {
            echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), "\n";
        } else {
            $this->renderText($report);
        }

        $fails = array_filter(
            $report['checks'],
            static fn (array $c): bool => $c['status'] === 'fail'
        );

        return $fails === [] ? 0 : 2;
    }

    /* ------------------------------------------------------------------
     | Rendering
     * ------------------------------------------------------------------ */

    protected function renderText(array $report): void
    {
        $icons = [
            'ok'   => "\033[32m✔\033[0m",
            'warn' => "\033[33m▲\033[0m",
            'fail' => "\033[31m✘\033[0m",
            'info' => "\033[36m•\033[0m",
        ];

        $this->writeln('');
        $this->writeln("\033[1m  Socialite Doctor\033[0m — " . $report['generated_at']);

        $groups = [];
        foreach ($report['checks'] as $check) {
            $groups[$check['group']][] = $check;
        }

        foreach ($groups as $group => $checks) {
            $this->writeln('');
            $this->writeln("\033[1m  " . ucfirst((string) $group) . "\033[0m");
            foreach ($checks as $c) {
                $badge = $icons[$c['status']] ?? $c['status'];
                $hint  = ($c['hint'] ?? '') !== '' ? '  ' . $c['hint'] : '';
                $this->writeln(sprintf('    %s %-22s%s', $badge, $c['name'], $hint));
            }
        }

        if (!empty($report['providers'])) {
            $this->writeln('');
            $this->writeln("\033[1m  Provider details\033[0m");
            $rows = [];
            foreach ($report['providers'] as $p) {
                $reach = match (true) {
                    !array_key_exists('reachable', $p) => '-',
                    $p['reachable'] === true           => 'ok (' . ($p['http_status'] ?? '?') . ')',
                    default                            => 'UNREACHABLE',
                };
                $rows[] = [
                    $p['label'],
                    strtoupper($p['state']),
                    $p['in_ui'] ? 'ya' : 'tidak',
                    $p['endpoints']['authUrl'] ?? '-',
                    $reach,
                ];
            }
            $this->table(['Provider', 'Status', 'UI', 'Authorize URL', 'Ping'], $rows);
        }

        $actions = [];
        foreach ($report['checks'] as $c) {
            if ($c['status'] === 'warn' && $c['group'] === 'providers') {
                $actions[] = "Isi kredensial {$c['name']} di .env (lihat .env.example), lalu jalankan ulang doctor.";
            } elseif ($c['status'] === 'fail') {
                $actions[] = "[{$c['group']}/{$c['name']}] {$c['hint']}";
            }
        }

        $this->writeln('');
        if ($actions === []) {
            $this->success('  Semua pemeriksaan lulus. Stack social login siap dipakai.');
        } else {
            $this->writeln("\033[1m  Next actions\033[0m");
            foreach (array_unique($actions) as $a) {
                $this->writeln('    → ' . $a);
            }
            $this->writeln('    → Uji end-to-end tanpa kredensial asli: php bin/console socialite:test-flow');
        }
        $this->writeln('');
    }
}
