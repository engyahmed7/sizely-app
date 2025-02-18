<?php

namespace App\Services;

class MeasurementService
{
    private const CONFIDENCE_THRESHOLD = 0.5;

    public function calculateShoulderWidth(array $leftShoulder, array $rightShoulder): ?float
    {
        if (!$this->checkKeypointConfidence([$leftShoulder, $rightShoulder])) {
            return null;
        }

        return $this->calculateDistance(
            ['x' => $leftShoulder['x'], 'y' => $leftShoulder['y']],
            ['x' => $rightShoulder['x'], 'y' => $rightShoulder['y']]
        );
    }

    public function calculateChestLength(array $leftShoulder, array $rightShoulder, array $leftHip, array $rightHip): ?float
    {
        if (!$this->checkKeypointConfidence([$leftShoulder, $rightShoulder, $leftHip, $rightHip])) {
            return null;
        }

        $shoulderMidY = ($leftShoulder['y'] + $rightShoulder['y']) / 2;
        $hipMidY = ($leftHip['y'] + $rightHip['y']) / 2;

        return abs($shoulderMidY - $hipMidY);
    }

    public function calculateSleeveLength(array $shoulder, array $elbow, array $wrist): ?float
    {
        if (!$this->checkKeypointConfidence([$shoulder, $elbow, $wrist])) {
            return null;
        }

        $shoulderToElbow = $this->calculateDistance(
            ['x' => $shoulder['x'], 'y' => $shoulder['y']],
            ['x' => $elbow['x'], 'y' => $elbow['y']]
        );

        $elbowToWrist = $this->calculateDistance(
            ['x' => $elbow['x'], 'y' => $elbow['y']],
            ['x' => $wrist['x'], 'y' => $wrist['y']]
        );

        return $shoulderToElbow + $elbowToWrist;
    }

    public function calculateShirtLength(array $leftShoulder, array $rightShoulder, array $leftHip, array $rightHip): ?float
    {
        if (!$this->checkKeypointConfidence([$leftShoulder, $rightShoulder, $leftHip, $rightHip])) {
            return null;
        }

        $neckX = ($leftShoulder['x'] + $rightShoulder['x']) / 2;
        $neckY = ($leftShoulder['y'] + $rightShoulder['y']) / 2;

        $midHipX = ($leftHip['x'] + $rightHip['x']) / 2;
        $midHipY = ($leftHip['y'] + $rightHip['y']) / 2;

        return $this->calculateDistance(
            ['x' => $neckX, 'y' => $neckY],
            ['x' => $midHipX, 'y' => $midHipY]
        );
    }

    private function calculateDistance(array $point1, array $point2): float
    {
        return sqrt(pow($point2['x'] - $point1['x'], 2) + pow($point2['y'] - $point1['y'], 2));
    }

    private function checkKeypointConfidence(array $keypoints): bool
    {
        foreach ($keypoints as $keypoint) {
            if (!isset($keypoint['score']) || $keypoint['score'] < self::CONFIDENCE_THRESHOLD) {
                return false;
            }
        }
        return true;
    }

    public function calculatePixelToCentimeterRatio(float $measuredChestWidth, float $pixelChestWidth): float
    {
        return $measuredChestWidth / $pixelChestWidth;
    }
}
