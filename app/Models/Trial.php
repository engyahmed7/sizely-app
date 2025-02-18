<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trial extends Model
{
    protected $fillable = [
        'trial_name',
        // 'gender',
        'righteye',
        'lefteye',
        'rightshoulder',
        'leftshoulder',
        'rightElbow',
        'leftElbow',
        'rightWrist',
        'leftWrist',
        'rightHip',
        'leftHip',
        'rightAnkle',
        'leftAnkle',
        'rightKnee',
        'leftKnee',
        'shoulder_cm',
        'chest_cm',
        'image_data',
        'measurements',
        'face_image'
    ];

    protected $casts = [
        'righteye' => 'array',
        'lefteye' => 'array',
        'rightshoulder' => 'array',
        'leftshoulder' => 'array',
        'rightElbow' => 'array',
        'leftElbow' => 'array',
        'rightWrist' => 'array',
        'leftWrist' => 'array',
        'rightHip' => 'array',
        'leftHip' => 'array',
        'rightAnkle' => 'array',
        'leftAnkle' => 'array',
        'rightKnee' => 'array',
        'leftKnee' => 'array',
        'measurements' => 'array',
    ];
}
