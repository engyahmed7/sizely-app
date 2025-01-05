<?php

namespace App\Http\Controllers;

use App\Models\Trial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TrialController extends Controller
{
    private const REFERENCE_EYE_DISTANCE_MALE_CM = 6.4;
    private const REFERENCE_EYE_DISTANCE_FEMALE_CM = 6.17;
    private const CHEST_CIRCUMFERENCE_FACTOR = 2.5;
    private const WAIST_CIRCUMFERENCE_FACTOR = 2.5;
    private const NECK_CIRCUMFERENCE_FACTOR = 0.5;

    private const PER_PAGE = 12;

    public function index()
    {
        $trials = Trial::orderBy('created_at', 'desc')
            ->paginate(self::PER_PAGE);

        return view('trial.index', compact('trials'));
    }

    public function create()
    {
        return view('trial.create');
    }

    public function store(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'trial_name' => 'required|string|max:255',
                'gender' => 'required|in:male,female',
                'data' => 'required|array',
            ]);

            $data = $request->input('data');

            // Extract points
            $rightEye = $data['rightEye'];
            $leftEye = $data['leftEye'];
            $nose = $data['nose'];
            $rightShoulder = $data['rightShoulder'];
            $leftShoulder = $data['leftShoulder'];
            $leftElbow = $data['leftElbow'];
            $rightElbow = $data['rightElbow'];
            $leftWrist = $data['leftWrist'];
            $rightWrist = $data['rightWrist'];
            $leftHip = $data['leftHip'];
            $rightHip = $data['rightHip'];
            $leftAnkle = $data['leftAnkle'];

            // Calculate pixel distances
            $eyePixelDistance = $this->calculatePixelDistance($leftEye, $rightEye);
            $shoulderPixelDistance = $this->calculatePixelDistance($leftShoulder, $rightShoulder);

            // Get pixel to cm ratio using eye distance as reference
            $pixelToCmRatio = $this->getPixelToCmRatio($eyePixelDistance, $validatedData['gender']);

            // Calculate all measurements in centimeters
            $measurements = $this->calculateAllMeasurements(
                $leftEye,
                $rightEye,
                $nose,
                $leftShoulder,
                $rightShoulder,
                $leftElbow,
                $rightElbow,
                $leftWrist,
                $rightWrist,
                $leftHip,
                $rightHip,
                $leftAnkle,
                $pixelToCmRatio
            );

            $trial = Trial::create([
                'trial_name' => $validatedData['trial_name'],
                'righteye' => $rightEye,
                'lefteye' => $leftEye,
                'rightshoulder' => $rightShoulder,
                'leftshoulder' => $leftShoulder,
                'rightElbow' => $rightElbow,
                'leftElbow' => $leftElbow,
                'rightWrist' => $rightWrist,
                'leftWrist' => $leftWrist,
                'rightHip' => $rightHip,
                'leftHip' => $leftHip,
                'gender' => $validatedData['gender'],
                'image_data' => $data['image'] ?? null,
                'measurements' => $measurements
            ]);

            return response()->json([
                'success' => true,
                'trial' => $trial,
                'measurements' => $measurements
            ], 201);
        } catch (\Exception $e) {
            Log::error('Trial creation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function calculatePixelDistance($point1, $point2)
    {
        $deltaX = $point2['x'] - $point1['x'];
        $deltaY = $point2['y'] - $point1['y'];
        return sqrt($deltaX * $deltaX + $deltaY * $deltaY);
    }

    private function getPixelToCmRatio(float $eyeDistance, string $gender): float
    {
        $referenceEyeDistanceCM = $gender === 'male'
            ? self::REFERENCE_EYE_DISTANCE_MALE_CM
            : self::REFERENCE_EYE_DISTANCE_FEMALE_CM;
        return $referenceEyeDistanceCM / $eyeDistance;
    }

    private function calculateAllMeasurements(
        $leftEye,
        $rightEye,
        $nose,
        $leftShoulder,
        $rightShoulder,
        $leftElbow,
        $rightElbow,
        $leftWrist,
        $rightWrist,
        $leftHip,
        $rightHip,
        $leftAnkle,
        $pixelToCmRatio
    ) {
        // Shoulder Width
        $shoulderWidth = $this->calculatePixelDistance($leftShoulder, $rightShoulder) * $pixelToCmRatio;

        $chestCircumference = $shoulderWidth * self::CHEST_CIRCUMFERENCE_FACTOR;

        // Sleeve Length -> left arm
        $shoulderToElbow = $this->calculatePixelDistance($leftShoulder, $leftElbow);
        $elbowToWrist = $this->calculatePixelDistance($leftElbow, $leftWrist);
        $sleeveLength = ($shoulderToElbow + $elbowToWrist) * $pixelToCmRatio;

        // Waist 
        $waistWidth = $this->calculatePixelDistance($leftHip, $rightHip) * $pixelToCmRatio;
        $waistCircumference = $waistWidth * self::WAIST_CIRCUMFERENCE_FACTOR;

        // Neck  
        $neckCircumference = $shoulderWidth * self::NECK_CIRCUMFERENCE_FACTOR;

        //shirt  
        $noseToHip = $this->calculatePixelDistance($nose, $leftHip);
        $shirtLength = $noseToHip * $pixelToCmRatio;

        // body height 
        $noseToAnkle = $this->calculatePixelDistance($nose, $leftAnkle);
        $bodyHeight = $noseToAnkle * $pixelToCmRatio;


        return [
            'chest_circumference' => round($chestCircumference, 2),
            'shoulder_width' => round($shoulderWidth, 2),
            'sleeve_length' => round($sleeveLength, 2),
            'shirt_length' => round($shirtLength, 2),
            'waist_circumference' => round($waistCircumference, 2),
            'neck_circumference' => round($neckCircumference, 2),
            'body_height' => round($bodyHeight, 2),
        ];
    }

    public function show(Trial $trial)
    {
        return view('trial.show', compact('trial'));
    }
}
