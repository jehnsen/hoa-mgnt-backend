<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Amenity;
use Illuminate\Database\Seeder;

/**
 * Seeds the 5 shared facilities available inside Springdale Village.
 *
 * All amenities are active at the time of seeding unless explicitly marked
 * is_active = false (e.g., a facility under renovation).
 */
class AmenitySeeder extends Seeder
{
    public function run(): void
    {
        $amenities = [
            // ──────────────────────────────────────────────────────────────────
            // 1. Swimming Pool
            // ──────────────────────────────────────────────────────────────────
            [
                'name'        => 'Swimming Pool',
                'description' => 'Outdoor lap pool (25m × 10m, depth 1.0m – 1.8m). Open to all registered '
                    . 'homeowners and their immediate family members. Children under 12 must be supervised '
                    . 'by an adult at all times. Operating hours: Mon–Sun, 6:00 AM – 6:00 PM. '
                    . 'Shower rooms and changing facilities are located adjacent to the pool.',
                'location'    => 'HOA Common Area – Near the Multipurpose Hall, Springdale Village',
                'capacity'    => 30,
                'is_active'   => true,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 2. Multipurpose Hall / Clubhouse
            // ──────────────────────────────────────────────────────────────────
            [
                'name'        => 'Multipurpose Hall',
                'description' => 'Air-conditioned hall suitable for meetings, general assemblies, parties, and '
                    . 'community events. Equipped with 10 round tables, 80 stackable chairs, a projector and '
                    . '100" screen, a public address system, and a small pantry. Caterers may bring external '
                    . 'equipment with prior HOA approval. No cooking inside the hall. Alcohol is permitted '
                    . 'only during approved private events after 6:00 PM. Maximum standing capacity: 120 persons.',
                'location'    => 'HOA Clubhouse Building, Ground Floor – Springdale Village',
                'capacity'    => 80,
                'is_active'   => true,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 3. Fitness Center / Gym
            // ──────────────────────────────────────────────────────────────────
            [
                'name'        => 'Fitness Center',
                'description' => 'Free-weights area (dumbbells 2.5kg–30kg, barbell sets), 2 treadmills, '
                    . '1 stationary bike, 1 elliptical trainer, 1 multi-station cable machine, and bench press. '
                    . 'Open only to homeowners and residents aged 18 and above. Guests may use the gym only when '
                    . 'accompanied by the registered homeowner. Proper gym attire (closed shoes, no sleeveless '
                    . 'shirts) required. Operating hours: Mon–Sun, 5:30 AM – 9:00 PM.',
                'location'    => 'HOA Clubhouse Building, Second Floor – Springdale Village',
                'capacity'    => 15,
                'is_active'   => true,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 4. Basketball Court
            // ──────────────────────────────────────────────────────────────────
            [
                'name'        => 'Basketball Court',
                'description' => 'Full-size covered basketball court with rubberized flooring (28m × 15m). '
                    . 'Two adjustable hoops (standard 10 ft). Available for basketball, badminton (with nets '
                    . 'available at HOA office on request), and other court sports. Outdoor floodlights available '
                    . 'until 10:00 PM. Court is first-come, first-served for casual use; reservations required '
                    . 'for inter-block tournaments or events. Non-marking rubber soles required.',
                'location'    => 'HOA Common Area – Block 3 side, Springdale Village',
                'capacity'    => 20,
                'is_active'   => true,
            ],

            // ──────────────────────────────────────────────────────────────────
            // 5. Function Room (currently under renovation)
            // ──────────────────────────────────────────────────────────────────
            [
                'name'        => 'Function Room',
                'description' => 'Smaller air-conditioned meeting room suitable for board meetings, committee '
                    . 'hearings, and small gatherings of up to 20 persons. Equipped with a long conference table, '
                    . '20 chairs, a whiteboard, and a 65" smart TV. Currently UNAVAILABLE – undergoing interior '
                    . 'renovation (new flooring and aircon replacement). Estimated completion: July 15, 2026.',
                'location'    => 'HOA Clubhouse Building, Ground Floor (left wing) – Springdale Village',
                'capacity'    => 20,
                'is_active'   => false,
            ],
        ];

        foreach ($amenities as $data) {
            Amenity::create($data);
        }

        $active   = Amenity::where('is_active', true)->count();
        $inactive = Amenity::where('is_active', false)->count();

        $this->command->info("  ✔ Seeded " . Amenity::count() . " amenities ({$active} active, {$inactive} inactive).");
    }
}
