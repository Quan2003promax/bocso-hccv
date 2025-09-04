<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServiceRegistration;
use Illuminate\Support\Facades\Storage;

class UpdateMimeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $registrations = ServiceRegistration::whereNotNull('document_file')
            ->whereNull('document_mime_type')
            ->get();

        echo "Found " . $registrations->count() . " registrations without MIME type\n";

        foreach ($registrations as $registration) {
            if ($registration->document_file && Storage::disk('public')->exists($registration->document_file)) {
                $filePath = Storage::disk('public')->path($registration->document_file);
                $mimeType = mime_content_type($filePath);
                
                if ($mimeType) {
                    $registration->update(['document_mime_type' => $mimeType]);
                    echo "Updated ID {$registration->id}: {$registration->document_original_name} -> {$mimeType}\n";
                } else {
                    echo "Could not determine MIME type for ID {$registration->id}: {$registration->document_original_name}\n";
                }
            }
        }

        echo "MIME type update completed!\n";
    }
}
