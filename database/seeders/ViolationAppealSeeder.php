<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\ViolationStatus;
use App\Models\User;
use App\Models\Violation;
use App\Models\ViolationAppeal;
use Illuminate\Database\Seeder;

/**
 * Seeds 1 violation appeal record for Springdale Village.
 *
 * The appeal corresponds to the "Appealed" violation at B1-L04
 * (Unauthorized Structure Modification – Front Grille Extension)
 * seeded by ViolationSeeder.
 */
class ViolationAppealSeeder extends Seeder
{
    public function run(): void
    {
        $violation = Violation::where('status', ViolationStatus::Appealed)->firstOrFail();

        $appellant = User::where('email', 'manuel.pascual@outlook.com')->firstOrFail();

        ViolationAppeal::create([
            'violation_id' => $violation->id,
            'appellant_id' => $appellant->id,
            'notes'        => "I respectfully contest Violation Notice #VN-2026-04-006 dated April 15, 2026 "
                . "regarding the alleged encroachment of our front fence grille extension at B1-L04, "
                . "Springdale Village.\n\n"
                . "GROUNDS FOR APPEAL:\n\n"
                . "1. SECURITY NECESSITY – The 60-cm grille extension was installed after three (3) "
                . "separate incidents of motorcycle theft were reported in Block 1 between January and "
                . "March 2026. The extension covers a gap between the fence and the carport post that "
                . "allowed unauthorized entry. Barangay Incident Reports IR-2026-0112, IR-2026-0198, "
                . "and IR-2026-0271 are attached as evidence.\n\n"
                . "2. DIMENSION DISPUTE – Our property survey plan (TCT No. T-1182734) indicates that "
                . "the property boundary extends to the edge of the concrete curb. The extension does "
                . "not encroach on a dedicated HOA easement as stated in the violation notice. "
                . "We respectfully request an independent survey be conducted at HOA expense.\n\n"
                . "3. GOOD FAITH COMPLIANCE – We are willing to reduce the extension height to the "
                . "maximum allowed under Article V of the Deed of Restrictions (2.2 meters) and to "
                . "apply for a retroactive Renovation Clearance from the Architectural Committee "
                . "should the board find partial merit in the notice.\n\n"
                . "We request a hearing before the Board at the earliest available schedule. "
                . "We remain committed to complying with all HOA regulations.\n\n"
                . "Respectfully,\nManuel T. Pascual\nB1-L04, Springdale Village\nMay 3, 2026",
            'evidence_images' => [
                [
                    'path'        => 'appeals/2026/05/b1-l04-appeal-fence-survey.jpg',
                    'description' => 'Property survey plan excerpt showing boundary line at curb edge',
                    'uploaded_at' => '2026-05-03 10:15:00',
                ],
                [
                    'path'        => 'appeals/2026/05/b1-l04-appeal-incident-report-1.jpg',
                    'description' => 'Barangay Incident Report IR-2026-0112 – first motorcycle theft',
                    'uploaded_at' => '2026-05-03 10:17:00',
                ],
                [
                    'path'        => 'appeals/2026/05/b1-l04-appeal-incident-report-2.jpg',
                    'description' => 'Barangay Incident Report IR-2026-0198 – second motorcycle theft',
                    'uploaded_at' => '2026-05-03 10:18:00',
                ],
                [
                    'path'        => 'appeals/2026/05/b1-l04-appeal-fence-photo.jpg',
                    'description' => "Photo of the fence extension from inside the property showing the gap it addresses",
                    'uploaded_at' => '2026-05-03 10:20:00',
                ],
            ],
        ]);

        $this->command->info('  ✔ Seeded ' . ViolationAppeal::count() . ' violation appeal(s).');
    }
}
