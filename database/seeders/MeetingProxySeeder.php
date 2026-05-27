<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\MeetingStatus;
use App\Models\Meeting;
use App\Models\MeetingProxy;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeds 2 meeting proxy records for the upcoming AGA 2026.
 *
 * Both grantors are residents unable to attend the Annual General Assembly
 * on June 14, 2026. They have authorized another homeowner to represent them.
 * Proxies must be submitted in writing per HOA By-Laws Article VI, Section 4.
 */
class MeetingProxySeeder extends Seeder
{
    public function run(): void
    {
        $aga = Meeting::where('status', MeetingStatus::Scheduled)->firstOrFail();

        $residents = User::where('role', 'resident')->get()->keyBy('email');

        // ── Proxy 1 ───────────────────────────────────────────────────────────
        // Grantor: Teresita Ramos (B3-L03) – traveling abroad on June 14
        // Proxy:   Diana Navarro (B3-L01) – designated representative
        MeetingProxy::create([
            'meeting_id' => $aga->id,
            'grantor_id' => $residents['teresita.ramos@yahoo.com']->id,
            'proxy_id'   => $residents['diana.navarro@gmail.com']->id,
            'is_active'  => true,
        ]);

        // ── Proxy 2 ───────────────────────────────────────────────────────────
        // Grantor: Ana Lucia Mendoza (B2-L03) – maternity leave / medical reason
        // Proxy:   Cristina Villanueva (B2-L01) – next-door neighbor
        MeetingProxy::create([
            'meeting_id' => $aga->id,
            'grantor_id' => $residents['analucia.mendoza@yahoo.com']->id,
            'proxy_id'   => $residents['cristina.villanueva@gmail.com']->id,
            'is_active'  => true,
        ]);

        $this->command->info('  ✔ Seeded ' . MeetingProxy::count() . ' meeting proxy record(s) for AGA 2026.');
    }
}
