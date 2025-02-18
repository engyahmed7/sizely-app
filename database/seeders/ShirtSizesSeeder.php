<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ShirtSizesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $filePath = database_path('seeders/Min_Max_Sizes.csv');

        if (($handle = fopen($filePath, 'r')) !== false) {
            $header = null;
            $data = [];

            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (!$header) {
                    $header = $row; 
                    continue;
                }

                $data[] = array_combine($header, $row);
            }

            fclose($handle);

            foreach ($data as &$entry) {
                $entry = [
                    'size' => $entry['Size'],
                    'material' => $entry['Material'],
                    'style' => $entry['Style'],
                    'chest_circumference_min' => $entry['Chest Circumference (cm)_min'],
                    'chest_circumference_max' => $entry['Chest Circumference (cm)_max'],
                    'shoulder_width_min' => $entry['Shoulder Width (cm)_min'],
                    'shoulder_width_max' => $entry['Shoulder Width (cm)_max'],
                    'sleeve_length_min' => $entry['Sleeve Length (cm)_min'],
                    'sleeve_length_max' => $entry['Sleeve Length (cm)_max'],
                    'shirt_length_min' => $entry['Shirt Length (cm)_min'],
                    'shirt_length_max' => $entry['Shirt Length (cm)_max'],
                    'waist_circumference_min' => $entry['Waist Circumference (cm)_min'],
                    'waist_circumference_max' => $entry['Waist Circumference (cm)_max'],
                    'neck_circumference_min' => $entry['Neck Circumference (cm)_min'],
                    'neck_circumference_max' => $entry['Neck Circumference (cm)_max'],
                    'tolerance' => '2',
                ];
            }

            DB::table('sizes')->insert($data);
        }
    }
}
