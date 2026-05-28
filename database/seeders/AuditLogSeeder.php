<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\AmenityBooking;
use App\Models\AuditLog;
use App\Models\BoardElection;
use App\Models\Invoice;
use App\Models\User;
use App\Models\Violation;
use Illuminate\Database\Seeder;

/**
 * Seeds realistic audit trail records that would have been generated had
 * the seeded violations, invoices, bookings, and elections been processed
 * through the service layer (which calls AuditLogger) instead of directly
 * via Eloquent.
 *
 * Audit entry categories:
 *   violation            → status_updated (draft→issued, issued→{appealed|paid|resolved}, etc.)
 *   invoice              → payment_recorded, cancelled
 *   amenity_booking      → booking_status_changed (pending→confirmed / pending→cancelled)
 *   board_election       → election_status_changed (draft→nominations_open→…→certified)
 */
class AuditLogSeeder extends Seeder
{
    public function run(): void
    {
        $president = User::where('email', 'rodrigo.aquino@springdale-hoa.ph')->firstOrFail();
        $treasurer = User::where('email', 'lourdes.santos@springdale-hoa.ph')->firstOrFail();
        $secretary = User::where('email', 'jose.bautista@springdale-hoa.ph')->firstOrFail();
        $admin     = User::where('email', 'admin@springdale-hoa.ph')->firstOrFail();

        $logs = [];

        // ── Violations ────────────────────────────────────────────────────────
        // Build one audit entry per status transition, per violation, in
        // chronological order.  Draft violations never enter the audit trail
        // (they were logged but not yet acted upon by the board).
        $transitionsByStatus = [
            // issued violations: one entry (draft → issued)
            'issued' => [
                ['from' => 'draft', 'to' => 'issued'],
            ],
            // appealed violations: two entries
            'appealed' => [
                ['from' => 'draft',   'to' => 'issued'],
                ['from' => 'issued',  'to' => 'appealed'],
            ],
            // paid violations: two entries
            'paid' => [
                ['from' => 'draft',  'to' => 'issued'],
                ['from' => 'issued', 'to' => 'paid'],
            ],
            // resolved violations: varies by description
            // B2-L01 (pet loose) and B3-L02 (burning) – fine paid first, then resolved
            'resolved' => [
                ['from' => 'draft',   'to' => 'issued'],
                ['from' => 'issued',  'to' => 'paid'],
                ['from' => 'paid',    'to' => 'resolved'],
            ],
        ];

        // Map each violation to its actor and a realistic base timestamp
        $violationMeta = [
            // unit_number => [actor, issuedAt, resolvedAt (if any)]
            'B1-L02' => ['actor' => $president->id, 'issued_at' => '2026-05-14 10:00:00'],
            'B3-L01' => ['actor' => $president->id, 'issued_at' => '2026-05-09 14:30:00'],
            'B4-L03' => ['actor' => $president->id, 'issued_at' => '2026-04-28 09:00:00'],
            'B1-L04' => ['actor' => $president->id, 'issued_at' => '2026-04-15 10:00:00', 'appealed_at' => '2026-05-03 14:00:00'],
            'B2-L03' => ['actor' => $president->id, 'issued_at' => '2026-03-25 09:00:00', 'paid_at' => '2026-04-02 11:00:00'],
            'B5-L01' => ['actor' => $president->id, 'issued_at' => '2026-02-20 11:00:00', 'paid_at' => '2026-03-10 14:30:00'],
            'B2-L01' => ['actor' => $president->id, 'issued_at' => '2026-01-16 10:00:00', 'paid_at' => '2026-01-28 10:00:00', 'resolved_at' => '2026-01-31 15:00:00'],
            'B3-L02' => ['actor' => $president->id, 'issued_at' => '2025-12-22 09:00:00', 'paid_at' => '2026-01-05 10:00:00', 'resolved_at' => '2026-01-07 10:00:00'],
        ];

        $violations = Violation::whereIn('status', ['issued', 'appealed', 'paid', 'resolved'])
                               ->with('property')
                               ->get();

        foreach ($violations as $violation) {
            $unit   = $violation->property->unit_number;
            $meta   = $violationMeta[$unit] ?? null;
            $status = $violation->status->value;
            $actorId = $meta['actor'] ?? $president->id;

            $steps = $transitionsByStatus[$status] ?? [];
            foreach ($steps as $step) {
                $ts = match($step['to']) {
                    'issued'   => $meta['issued_at']   ?? $violation->issued_at?->toDateTimeString() ?? now()->toDateTimeString(),
                    'appealed' => $meta['appealed_at'] ?? now()->toDateTimeString(),
                    'paid'     => $meta['paid_at']     ?? now()->toDateTimeString(),
                    'resolved' => $meta['resolved_at'] ?? $violation->resolved_at?->toDateTimeString() ?? now()->toDateTimeString(),
                    default    => now()->toDateTimeString(),
                };

                $logs[] = [
                    'auditable_type' => 'violation',
                    'auditable_uuid' => $violation->uuid,
                    'action'         => 'status_updated',
                    'actor_id'       => $actorId,
                    'old_values'     => json_encode(['status' => $step['from']]),
                    'new_values'     => json_encode(['status' => $step['to']]),
                    'ip_address'     => '192.168.1.10',
                    'created_at'     => $ts,
                ];
            }
        }

        // ── Invoices – payment_recorded ───────────────────────────────────────
        $paidInvoices = Invoice::where('status', 'paid')
                               ->with('payments')
                               ->get();

        foreach ($paidInvoices as $invoice) {
            foreach ($invoice->payments as $payment) {
                $logs[] = [
                    'auditable_type' => 'invoice',
                    'auditable_uuid' => $invoice->uuid,
                    'action'         => 'payment_recorded',
                    'actor_id'       => $treasurer->id,
                    'old_values'     => null,
                    'new_values'     => json_encode([
                        'payment_uuid' => $payment->uuid,
                        'amount'       => $payment->amount,
                        'method'       => $payment->payment_method->value,
                        'new_status'   => 'paid',
                    ]),
                    'ip_address'     => '192.168.1.11',
                    'created_at'     => $payment->paid_at?->toDateTimeString() ?? $payment->created_at->toDateTimeString(),
                ];
            }
        }

        // Partial invoices (payment was recorded but didn't fully settle)
        $partialInvoices = Invoice::where('status', 'partial')
                                  ->with('payments')
                                  ->get();

        foreach ($partialInvoices as $invoice) {
            foreach ($invoice->payments as $payment) {
                $logs[] = [
                    'auditable_type' => 'invoice',
                    'auditable_uuid' => $invoice->uuid,
                    'action'         => 'payment_recorded',
                    'actor_id'       => $treasurer->id,
                    'old_values'     => null,
                    'new_values'     => json_encode([
                        'payment_uuid' => $payment->uuid,
                        'amount'       => $payment->amount,
                        'method'       => $payment->payment_method->value,
                        'new_status'   => 'partial',
                    ]),
                    'ip_address'     => '192.168.1.11',
                    'created_at'     => $payment->paid_at?->toDateTimeString() ?? $payment->created_at->toDateTimeString(),
                ];
            }
        }

        // Cancelled invoices
        $cancelledInvoices = Invoice::where('status', 'cancelled')->get();

        foreach ($cancelledInvoices as $invoice) {
            $logs[] = [
                'auditable_type' => 'invoice',
                'auditable_uuid' => $invoice->uuid,
                'action'         => 'cancelled',
                'actor_id'       => $treasurer->id,
                'old_values'     => json_encode(['status' => 'pending']),
                'new_values'     => json_encode(['status' => 'cancelled']),
                'ip_address'     => '192.168.1.11',
                'created_at'     => $invoice->updated_at->toDateTimeString(),
            ];
        }

        // ── Amenity Bookings – booking_status_changed ─────────────────────────
        $confirmedBookings = AmenityBooking::where('status', 'confirmed')->get();

        foreach ($confirmedBookings as $booking) {
            $logs[] = [
                'auditable_type' => 'amenity_booking',
                'auditable_uuid' => $booking->uuid,
                'action'         => 'booking_status_changed',
                'actor_id'       => $secretary->id,
                'old_values'     => json_encode(['status' => 'pending']),
                'new_values'     => json_encode(['status' => 'confirmed']),
                'ip_address'     => '192.168.1.10',
                'created_at'     => $booking->updated_at->toDateTimeString(),
            ];
        }

        $cancelledBookings = AmenityBooking::where('status', 'cancelled')->get();

        foreach ($cancelledBookings as $booking) {
            $logs[] = [
                'auditable_type' => 'amenity_booking',
                'auditable_uuid' => $booking->uuid,
                'action'         => 'booking_status_changed',
                'actor_id'       => $secretary->id,
                'old_values'     => json_encode(['status' => 'confirmed']),
                'new_values'     => json_encode(['status' => 'cancelled']),
                'ip_address'     => '192.168.1.10',
                'created_at'     => $booking->cancelled_at?->toDateTimeString() ?? $booking->updated_at->toDateTimeString(),
            ];
        }

        // ── Board Elections – election_status_changed ─────────────────────────
        // Past election (certified): 4 transitions
        $pastElection = BoardElection::where('status', 'certified')->first();

        if ($pastElection !== null) {
            $electionTransitions = [
                ['from' => 'draft',            'to' => 'nominations_open', 'at' => '2024-06-29 09:00:00'],
                ['from' => 'nominations_open', 'to' => 'voting_open',      'at' => '2024-07-13 14:00:00'],
                ['from' => 'voting_open',      'to' => 'closed',           'at' => '2024-07-13 17:00:00'],
                ['from' => 'closed',           'to' => 'certified',        'at' => '2024-07-20 10:00:00'],
            ];

            foreach ($electionTransitions as $t) {
                $logs[] = [
                    'auditable_type' => 'board_election',
                    'auditable_uuid' => $pastElection->uuid,
                    'action'         => 'election_status_changed',
                    'actor_id'       => $admin->id,
                    'old_values'     => json_encode(['status' => $t['from']]),
                    'new_values'     => json_encode(['status' => $t['to']]),
                    'ip_address'     => '192.168.1.100',
                    'created_at'     => $t['at'],
                ];
            }
        }

        // Upcoming election (nominations_open): 1 transition
        $upcomingElection = BoardElection::where('status', 'nominations_open')->first();

        if ($upcomingElection !== null) {
            $logs[] = [
                'auditable_type' => 'board_election',
                'auditable_uuid' => $upcomingElection->uuid,
                'action'         => 'election_status_changed',
                'actor_id'       => $admin->id,
                'old_values'     => json_encode(['status' => 'draft']),
                'new_values'     => json_encode(['status' => 'nominations_open']),
                'ip_address'     => '192.168.1.100',
                'created_at'     => '2026-05-01 09:00:00',
            ];
        }

        // ── Insert all logs in one batch ──────────────────────────────────────
        foreach (array_chunk($logs, 100) as $chunk) {
            AuditLog::insert($chunk);
        }

        $this->command->info(sprintf('  ✔ Seeded %d audit log entries.', AuditLog::count()));
    }
}
