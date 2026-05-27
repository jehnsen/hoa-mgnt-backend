<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceType: string
{
    case MonthlyDues       = 'monthly_dues';
    case SpecialAssessment = 'special_assessment';
    case WaterBill         = 'water_bill';
    case ParkingFee        = 'parking_fee';
    case Utility           = 'utility';
    case ViolationFine      = 'violation_fine';
    case AmenityBookingFee  = 'amenity_booking_fee';

    public function label(): string
    {
        return match($this) {
            self::MonthlyDues       => 'Monthly Dues',
            self::SpecialAssessment => 'Special Assessment',
            self::WaterBill         => 'Water Bill',
            self::ParkingFee        => 'Parking Fee',
            self::Utility           => 'Utility',
            self::ViolationFine     => 'Violation Fine',
            self::AmenityBookingFee => 'Amenity Booking Fee',
        };
    }
}
