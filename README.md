# Sizely AI Detects Sizes POS 

[![Laravel Version](https://img.shields.io/badge/Laravel-10.x-%23FF2D20?logo=laravel)](https://laravel.com) [![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-%23777BB4?logo=php)](https://www.php.net/)  

[![Node.js](https://img.shields.io/badge/Node.js-18.x-339933?style=for-the-badge&logo=nodedotjs&logoColor=white)](https://nodejs.org/) [![TensorFlow Version](https://img.shields.io/badge/TensorFlow-2.x-FF6F00?style=for-the-badge&logo=tensorflow&logoColor=white)](https://www.tensorflow.org/) [![PoseNet](https://img.shields.io/badge/PoseNet-000000?style=for-the-badge&logo=tensorflow&logoColor=white)](https://github.com/tensorflow/tfjs-models/tree/master/posenet)  

## Overview

**Sizely AI Detects Sizes POS** is a web application that combines face detection and pose estimation to calculate body measurements. The two-step AI workflow ensures accurate sizing by first aligning the user via face detection, then analyzing body posture with PoseNet. Includes a **retake button** for users to restart the camera if capture quality is unsatisfactory.

---

## Architecture Overview

```mermaid
graph TD
  A[Webcam Input] --> B(Face Detection)
  B --> C{Alignment Valid?}
  C -->|Yes| D(Pose Detection)
  C -->|No| B
  D --> E[Body Measurements]
  E --> F{User Satisfied?}
  F -->|No| A
  F -->|Yes| G(Laravel Backend)
```

---

## Key Features

### Two-Step Detection Workflow

```mermaid
sequenceDiagram
  participant User
  participant System
  User->>System: Start Webcam
  System->>System: Face Detection (Step 1)
  System-->>User: "Align Your Face"
  User->>System: Proper Positioning
  System->>System: Pose Detection (Step 2)
  System->>System: Calculate Measurements
  System-->>User: Display Results
  alt Retake Needed
    User->>System: Click Retake Button
    System->>User: Restart Camera
    System->>System: Reset Detection
  end
```

1. **Face Detection**  
   Ensures user is centered and at optimal distance from the camera.
2. **Pose Detection**  
   Uses PoseNet to detect 17 body keypoints for measurement extraction.
3. **Retake Functionality**  
   Users can restart the camera and recapture data at any stage using the retake button.

---

## Prerequisites

![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.3-777BB4?style=flat)
![Node.js](https://img.shields.io/badge/Node.js-%3E%3D14.x-339933?style=flat)

- Web browser with **WebGL support** (Chrome/Firefox recommended)
- Modern GPU (for accelerated TensorFlow.js computations)

---

## Installation & Setup

### 1. Clone Repository
```bash
git clone https://github.com/engyahmed7/sizely-app.git
```

### 2. Install Dependencies
```bash
composer install && npm install --legacy-peer-deps
```

### 3. Configure Environment
```bash
cp .env.example .env && php artisan key:generate
```

---

## System Design

```mermaid
graph LR
  A[Client-Side] -->|Video Stream| B(TensorFlow.js)
  B --> C[PoseNet Model]
  C --> D{Keypoints}
  D -->|JSON Data| E[Laravel API]
  E --> F[Body-Measure Library]
  F --> G[Size Metrics]
  G --> H{Retake?}
  H -->|Yes| A
```

---

## Acknowledgements

- **Machine Learning**: [TensorFlow.js](https://www.tensorflow.org/js) | [PoseNet](https://github.com/tensorflow/tfjs-models/tree/master/posenet)
- **Measurement Logic**: [body-measure](https://github.com/AI-Machine-Vision-Lab/body-measure)
- **Backend Framework**: [Laravel](https://laravel.com)

---
## Contributing
We welcome contributions from the community! To contribute, follow these steps:

1. **Fork** the repository.
2. **Create a new branch** following this naming convention: `feature/your-feature-name`.
3. **Make your changes** and ensure your code follows the project’s coding standards.
4. **Write tests** if applicable to ensure new functionality works as expected.
5. **Commit your changes** with a meaningful commit message.
6. **Push to your branch** on your forked repository.
7. **Open a Pull Request** with a clear description of your changes and link any relevant issues.
8. **Wait for a review** and address any feedback provided.

Your contributions help improve Vacation Tracker, and we appreciate your efforts!
