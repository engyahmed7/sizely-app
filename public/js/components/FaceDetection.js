import { bindPage, formData } from "./camera.js";

export default class FaceDetection {
    constructor() {
        this.$videoElement = null;
        this.$canvas = null;
        this.ctx = null;
        this.isSubmitting = false;
        this.detectionLoop = true;
        this.capturedImage = null;
        this.faceImageData = null;
    }

    async init(videoElementId, canvasId) {
        const cameraContainer = $("<div>", { class: "camera-container" });
        const cameraFrame = $("<div>", { class: "camera-frame" });
        const captureOverlay = $("<div>", { class: "capture-overlay" });
        const faceGuide = $("<div>", { class: "face-guide" });
        this.$videoElement = $(`#${videoElementId}`);
        this.$canvas = $(`#${canvasId}`);

        this.$videoElement.wrap(cameraFrame);
        const $parent = this.$videoElement.parent();
        $parent.append(captureOverlay, faceGuide);
        $parent.wrap(cameraContainer);

        const $buttonContainer = $("<div>", { class: "button-container" });
        const $captureBtn = $("<button>", {
            class: "btn btn-primary",
            html: '<i class="fas fa-camera"></i> Capture Photo',
            click: () => this.startCapture(),
        });
        $buttonContainer.append($captureBtn);
        $parent.parent().append($buttonContainer);

        this.ctx = this.$canvas[0].getContext("2d");

        try {
            await this.initCamera();
            this.showAlert(
                "Look straight at the camera and position your face within the guide",
                "warning"
            );
        } catch (error) {
            this.showAlert(
                "Camera initialization failed. Please check your permissions.",
                "error"
            );
            console.error("Error initializing camera:", error);
        }
    }

    showAlert(message, type = "error") {
        $(".alert").remove();

        const $alert = $("<div>", {
            class: `alert alert-${type}`,
        });

        let icon = "";
        switch (type) {
            case "success":
                icon = '<i class="fas fa-check-circle alert-icon"></i>';
                break;
            case "error":
                icon = '<i class="fas fa-exclamation-circle alert-icon"></i>';
                break;
            case "warning":
                icon = '<i class="fas fa-exclamation-triangle alert-icon"></i>';
                break;
        }

        $alert.html(`${icon}<span>${message}</span>`);
        $("body").append($alert);

        setTimeout(() => {
            $alert.css("animation", "fadeOut 0.3s ease-out forwards");
            setTimeout(() => $alert.remove(), 300);
        }, 3000);
    }

    async initCamera() {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({
                video: {
                    width: 640,
                    height: 480,
                    facingMode: "user",
                },
            });

            this.$videoElement[0].srcObject = stream;

            const $loadingOverlay = $("<div>", {
                class: "loading-overlay",
                html: '<div class="spinner"></div>',
            });
            this.$videoElement.parent().append($loadingOverlay);

            return new Promise((resolve) => {
                this.$videoElement.on("loadedmetadata", () => {
                    const videoElem = this.$videoElement[0];
                    videoElem.width = videoElem.videoWidth;
                    videoElem.height = videoElem.videoHeight;

                    const canvasElem = this.$canvas[0];
                    canvasElem.width = videoElem.videoWidth;
                    canvasElem.height = videoElem.videoHeight;

                    $loadingOverlay.fadeOut(300, function () {
                        $(this).remove();
                    });

                    resolve();
                });
            });
        } catch (error) {
            throw new Error("Camera access denied or device not available");
        }
    }

    startCapture() {
        $(".btn-primary").prop("disabled", true).addClass("disabled");
        this.showCountdown();
    }

    showCountdown() {
        const countdownElement = $("<div>", {
            class: "countdown",
            text: "4",
        }).appendTo(this.$videoElement.parent());

        let count = 4;
        const countdownInterval = setInterval(() => {
            count--;
            countdownElement.text(count).addClass("pulse");

            setTimeout(() => countdownElement.removeClass("pulse"), 200);

            if (count === 0) {
                clearInterval(countdownInterval);
                countdownElement.fadeOut(200, () => {
                    countdownElement.remove();

                    this.handleImageCapture();
                });
            }
        }, 1000);
    }

    async handleImageCapture() {
        this.detectionLoop = false;
        const videoElement = this.$videoElement[0];
        videoElement.pause();
        const stream = this.$videoElement[0].srcObject;

        const tempCanvas = document.createElement("canvas");
        tempCanvas.width = this.$videoElement[0].videoWidth;
        tempCanvas.height = this.$videoElement[0].videoHeight;
        const tempCtx = tempCanvas.getContext("2d");

        const centerX = tempCanvas.width / 2;
        const centerY = tempCanvas.height / 2;
        const size = Math.min(tempCanvas.width, tempCanvas.height) * 0.8;

        const topLeftX = centerX - size / 2;
        const topLeftY = centerY - size / 2;

        tempCtx.beginPath();
        tempCtx.rect(topLeftX, topLeftY, size, size);
        tempCtx.clip();

        tempCtx.scale(-1, 1);
        tempCtx.translate(-tempCanvas.width, 0);

        const videoAspectRatio =
            videoElement.videoWidth / videoElement.videoHeight;
        const canvasAspectRatio = tempCanvas.width / tempCanvas.height;

        let drawWidth, drawHeight;
        if (videoAspectRatio > canvasAspectRatio) {
            drawWidth = tempCanvas.width;
            drawHeight = tempCanvas.width / videoAspectRatio;
        } else {
            drawWidth = tempCanvas.height * videoAspectRatio;
            drawHeight = tempCanvas.height;
        }

        const offsetX = (tempCanvas.width - drawWidth) / 2;
        const offsetY = (tempCanvas.height - drawHeight) / 2;

        tempCtx.drawImage(
            videoElement,
            offsetX,
            offsetY,
            tempCanvas.width,
            tempCanvas.height,
            0,
            0,
            drawWidth,
            drawHeight
        );

        tempCtx.setTransform(1, 0, 0, 1, 0, 0);

        const imageData = tempCanvas.toDataURL("image/jpeg", 0.95);

        this.capturedImage = new Image();
        this.capturedImage.width = drawWidth;
        this.capturedImage.height = drawHeight;
        this.capturedImage.src = imageData;

        await new Promise((resolve) => {
            this.capturedImage.onload = resolve;
        });

        this.showCapturedImage();
    }

    showCapturedImage() {
        this.ctx.clearRect(0, 0, this.$canvas[0].width, this.$canvas[0].height);

        this.$videoElement.fadeOut(300, () => {
            const centerX = this.$canvas[0].width / 2;
            const centerY = this.$canvas[0].height / 2;
            const radius =
                Math.min(this.$canvas[0].width, this.$canvas[0].height) * 0.4;

            this.ctx.beginPath();
            this.ctx.arc(centerX, centerY, radius, 0, 2 * Math.PI);
            this.ctx.clip();

            this.ctx.drawImage(this.capturedImage, 0, 0);
            this.$canvas.fadeIn(300);

            $(".face-guide").fadeOut(300);
            $(".button-container")
                .empty()
                .append(
                    $("<button>", {
                        class: "btn btn-secondary",
                        html: '<i class="fas fa-redo"></i> Retake',
                        click: (e) => this.retake(e),
                    }),
                    $("<button>", {
                        class: "btn btn-primary",
                        html: '<i class="fas fa-check"></i> Continue',
                        click: (e) => this.continue(e),
                    })
                );
        });
    }

    retake(e) {
        if (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        const videoElement = this.$videoElement[0];
        videoElement.pause();

        this.ctx.clearRect(0, 0, this.$canvas[0].width, this.$canvas[0].height);
        this.capturedImage = null;

        this.$canvas.fadeOut(300, () => {
            this.$videoElement.fadeIn(300);
        });

        $(".button-container").remove();
        this.initCamera()
            .then(() => {
                const $buttonContainer = $("<div>", {
                    class: "button-container",
                });
                const $captureBtn = $("<button>", {
                    class: "btn btn-primary",
                    html: '<i class="fas fa-camera"></i> Capture Photo',
                    click: () => this.startCapture(),
                });
                $buttonContainer.append($captureBtn);
                $(".camera-container").append($buttonContainer);
                $(".face-guide").fadeIn(300);
            })
            .catch((error) => {
                console.error(
                    "Error reinitializing camera during retake:",
                    error
                );
            });
    }

    continue(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }

        $("#face_step").hide();

        $("#step2").show();

        try {
            // app();
            bindPage();
        } catch (error) {
            console.error("Error initializing PoseDetection:", error);
        }

        if (this.capturedImage) {
            formData.face_image = this.capturedImage.src;
        } else {
            console.error("Face image data is missing!");
        }
    }

    getFaceImageData() {
        return this.faceImageData;
    }
}
