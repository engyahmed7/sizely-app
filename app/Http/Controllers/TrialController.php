<?php

namespace App\Http\Controllers;

use App\Models\Trial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Services\MeasurementService;

class TrialController extends Controller
{
    private const PER_PAGE = 12;
    private $measurementService;

    public function __construct(MeasurementService $measurementService)
    {
        $this->measurementService = $measurementService;
    }

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
                'data' => 'required|array',
                'face_image' => 'required|string',
                'chest_width_mm' => 'required|numeric',
                'shoulder_width_mm' => 'required|numeric',

            ]);

            // Log::info('Validated Data: ' . json_encode($validatedData));

            $data = $request->input('data');

            $rightEye = $data['rightEye'] ?? null;
            $leftEye = $data['leftEye'] ?? null;
            $nose = $data['nose'] ?? null;
            $rightShoulder = $data['rightShoulder'] ?? null;
            $leftShoulder = $data['leftShoulder'] ?? null;
            $leftElbow = $data['leftElbow'] ?? null;
            $rightElbow = $data['rightElbow'] ?? null;
            $leftWrist = $data['leftWrist'] ?? null;
            $rightWrist = $data['rightWrist'] ?? null;
            $leftHip = $data['leftHip'] ?? null;
            $rightHip = $data['rightHip'] ?? null;
            $leftAnkle = $data['leftAnkle'] ?? null;
            $shoulderWidth = $validatedData['shoulder_width_mm'] / 10  ?? null;
            $chestWidth = $validatedData['chest_width_mm'] / 10  ?? null;

            $shoulderWidthPixels = $this->measurementService->calculateShoulderWidth(
                $data['leftShoulder'],
                $data['rightShoulder']
            );

            $chestLengthPixels = $this->measurementService->calculateChestLength(
                $data['leftShoulder'],
                $data['rightShoulder'],
                $data['leftHip'],
                $data['rightHip']
            );

            $leftSleevePixels = $this->measurementService->calculateSleeveLength(
                $data['leftShoulder'],
                $data['leftElbow'],
                $data['leftWrist']
            );

            $rightSleevePixels = $this->measurementService->calculateSleeveLength(
                $data['rightShoulder'],
                $data['rightElbow'],
                $data['rightWrist']
            );

            $shoulderWidthCm = $validatedData['shoulder_width_mm'] / 10;
            $chestWidthCm = $validatedData['chest_width_mm'] / 10;

            $pixelToCmRatio = $this->measurementService->calculatePixelToCentimeterRatio(
                $chestWidthCm,
                $chestLengthPixels
            );

            $measurements = [
                'shoulder_cm' => $shoulderWidthCm,
                'chest_cm' => $chestWidthCm,
                'shoulder_cm' => $shoulderWidthPixels * $pixelToCmRatio,
                'left_sleeve_cm' => $leftSleevePixels * $pixelToCmRatio,
                'right_sleeve_cm' => $rightSleevePixels * $pixelToCmRatio
            ];

            Log::info('Measurements: ' . json_encode($measurements));

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
                'image_data' => $data['image'] ?? null,
                'face_image' => $validatedData['face_image'] ?? null,
                'shoulder_cm' => $shoulderWidth,
                'chest_cm' => $chestWidth,
                "measurements" => json_encode($measurements),
            ]);

            $sizes = $this->getSizeByDimensions($shoulderWidth) ?? [];

            Log::info('Sizes: ' . json_encode($sizes));

            return response()->json([
                'success' => true,
                'trial' => $trial,
                'sizes' =>  json_encode($sizes)
            ], 201);
        } catch (\Exception $e) {
            Log::error('Trial creation error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function show(Trial $trial)
    {
        $shoulderWidth = $trial->shoulder_cm;
        $chestWidth = $trial->chest_cm;
        Log::info('measurements: ' . json_encode($trial->measurements));
        $sizes = $this->getSizeByDimensions($shoulderWidth);
        return view('trial.show', [
            'trial' => $trial,
            'shoulderWidth' => $shoulderWidth,
            'sizes' => json_encode($sizes),
            'measurements' => $trial->measurements,
            'chestWidth' => $chestWidth,
        ]);
    }
    function getSizeByDimensions($shoulderWidth)
    {

        $results = DB::table('sizes')
            ->select('size', 'material', 'style')
            ->where('shoulder_width_min', '<=', (float)$shoulderWidth)
            ->where('shoulder_width_max', '>=', (float)$shoulderWidth)
            ->get();

        return $results->toArray();
    }

    function destroy(Trial $trial)
    {
        $trial->delete();
        return redirect()->route('trials.index')->with('success', 'Trial deleted successfully.');
    }
}
