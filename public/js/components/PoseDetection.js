export default class PoseDetection {
    constructor() {
        this.formData = {
            data: {},
        };
        this.net = null;
        this.$videoElement = null;
        this.$canvas = null;
        this.ctx = null;
        this.isSubmitting = false;
        this.detectionLoop = true;
        this.capturedImage = null;
    }

    async init(videoElementId, canvasId) {
        this.$videoElement = $(`#${videoElementId}`);
        this.$canvas = $(`#${canvasId}`);
        this.ctx = this.$canvas[0].getContext("2d");

        try {
            this.net = await posenet.load();
            this.showAlert(messages.look_at_camera, "warning");
            await this.initCamera();
            this.detectPose();
        } catch (error) {
            this.showAlert(messages.init_error, "error");
            console.error("Error initializing PoseNet or camera:", error);
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
        const stream = await navigator.mediaDevices.getUserMedia({
            video: true,
        });
        this.$videoElement[0].srcObject = stream;

        return new Promise((resolve) => {
            this.$videoElement.on("loadedmetadata", () => {
                const videoElem = this.$videoElement[0];
                videoElem.width = videoElem.videoWidth;
                videoElem.height = videoElem.videoHeight;

                const canvasElem = this.$canvas[0];
                this.$canvas[0].width = videoElem.videoWidth;
                this.$canvas[0].height = videoElem.videoHeight;
                resolve();
            });
        });
    }

    async detectPose() {
        while (true) {
            const pose = await this.net.estimateSinglePose(
                this.$videoElement[0],
                {
                    flipHorizontal: true,
                }
            );
            this.ctx.clearRect(
                0,
                0,
                this.$canvas[0].width,
                this.$canvas[0].height
            );
            this.ctx.scale(-1, -1);
            this.ctx.translate(-this.$canvas[0].width, 0);
            this.ctx.drawImage(
                this.$videoElement[0],
                -this.$canvas[0].width,
                0,
                this.$canvas[0].width,
                this.$canvas[0].height
            );
            this.ctx.restore();

            this.drawDetections(pose.keypoints);

            const keypoints = pose.keypoints.filter(
                (point) =>
                    point.part === "nose" ||
                    point.part === "rightEye" ||
                    point.part === "leftEye" ||
                    point.part === "rightShoulder" ||
                    point.part === "leftShoulder" ||
                    point.part === "rightElbow" ||
                    point.part === "leftElbow" ||
                    point.part === "leftWrist" ||
                    point.part === "rightWrist" ||
                    point.part === "rightHip" ||
                    point.part === "leftHip" ||
                    point.part === "rightKnee" ||
                    point.part === "leftKnee" ||
                    point.part === "rightAnkle" ||
                    point.part === "leftAnkle"
            );
            console.log(keypoints);

            const allKeypointsValid = keypoints.every((point) => {
                console.log(point);
                return point.score >= 0.95;
            });

            console.log(allKeypointsValid);
            // console.log(keypoints);

            if (allKeypointsValid && !this.isSubmitting) {
                this.handleValidPose(keypoints);
                return;
            }

            await tf.nextFrame();
        }
    }

    // async handleImageCapture() {
    //     this.detectionLoop = false;

    //     const tempCanvas = document.createElement("canvas");
    //     tempCanvas.width = this.$videoElement[0].videoWidth;
    //     tempCanvas.height = this.$videoElement[0].videoHeight;
    //     const tempCtx = tempCanvas.getContext("2d");

    //     tempCtx.scale(-1, 1);
    //     tempCtx.translate(-tempCanvas.width, 0);
    //     tempCtx.drawImage(this.$videoElement[0], 0, 0);
    //     tempCtx.setTransform(1, 0, 0, 1, 0, 0);

    //     const imageData = tempCanvas.toDataURL("image/jpeg", 0.95);
    //     this.formData.data.image = imageData;

    //     const capturedImage = new Image();
    //     capturedImage.src = imageData;

    //     await new Promise((resolve) => {
    //         capturedImage.onload = resolve;
    //     });

    //     this.capturedImage = capturedImage;

    //     console.log("Captured image:", this.capturedImage);

    //     const stream = this.$videoElement[0].srcObject;
    //     const tracks = stream.getTracks();
    //     tracks.forEach((track) => track.stop());

    //     await this.processCapturedImage(imageData);
    // }

    // async processCapturedImage(imageData) {
    //     try {
    //         const pose = await this.net.estimateSinglePose(this.capturedImage, {
    //             flipHorizontal: true,
    //         });

    //         console.log("Captured image pose:", pose);
    //         this.ctx.clearRect(
    //             0,
    //             0,
    //             this.$canvas[0].width,
    //             this.$canvas[0].height
    //         );
    //         this.ctx.drawImage(this.capturedImage, 0, 0);

    //         const keypoints = pose.keypoints.filter(
    //             (point) =>
    //                 point.part === "rightEye" ||
    //                 point.part === "leftEye" ||
    //                 point.part === "rightShoulder" ||
    //                 point.part === "leftShoulder" ||
    //                 point.part === "rightElbow" ||
    //                 point.part === "leftElbow" ||
    //                 point.part === "leftWrist" ||
    //                 point.part === "rightWrist" ||
    //                 point.part === "rightHip" ||
    //                 point.part === "leftHip" ||
    //                 point.part === "nose" ||
    //                 point.part === "rightKnee" ||
    //                 point.part === "leftKnee" ||
    //                 point.part === "leftAnkle"
    //         );

    //         this.drawDetections(keypoints);

    //         this.handleValidPose(keypoints, imageData);
    //     } catch (error) {
    //         console.error("Error processing captured image:", error);
    //         this.showAlert("Error processing image", "error");
    //     }
    // }

    handleValidPose(keypoints) {
        this.isSubmitting = true;
        this.detectionLoop = false;

        const rightEye = keypoints.find((point) => point.part === "rightEye");
        const leftEye = keypoints.find((point) => point.part === "leftEye");

        const leftShoulder = keypoints.find(
            (point) => point.part === "leftShoulder"
        );
        const rightShoulder = keypoints.find(
            (point) => point.part === "rightShoulder"
        );
        const nose = keypoints.find((point) => point.part === "nose");
        const leftElbow = keypoints.find((point) => point.part === "leftElbow");
        const rightElbow = keypoints.find(
            (point) => point.part === "rightElbow"
        );
        const leftWrist = keypoints.find((point) => point.part === "leftWrist");
        const rightWrist = keypoints.find(
            (point) => point.part === "rightWrist"
        );
        const leftHip = keypoints.find((point) => point.part === "leftHip");
        const rightHip = keypoints.find((point) => point.part === "rightHip");
        const leftKnee = keypoints.find((point) => point.part === "leftKnee");
        const rightKnee = keypoints.find((point) => point.part === "rightKnee");
        const leftAnkle = keypoints.find((point) => point.part === "leftAnkle");
        const rightAnkle = keypoints.find(
            (point) => point.part === "rightAnkle"
        );

        this.formData.data = {
            rightEye: {
                x: rightEye.position.x,
                y: rightEye.position.y,
                score: rightEye.score,
            },
            leftEye: {
                x: leftEye.position.x,
                y: leftEye.position.y,
                score: leftEye.score,
            },
            nose: {
                x: nose.position.x,
                y: nose.position.y,
                score: nose.score,
            },
            rightShoulder: {
                x: rightShoulder.position.x,
                y: rightShoulder.position.y,
                score: rightShoulder.score,
            },
            leftShoulder: {
                x: leftShoulder.position.x,
                y: leftShoulder.position.y,
                score: leftShoulder.score,
            },
            leftElbow: {
                x: leftElbow.position.x,
                y: leftElbow.position.y,
                score: leftElbow.score,
            },
            rightElbow: {
                x: leftElbow.position.x,
                y: leftElbow.position.y,
                score: leftElbow.score,
            },
            leftWrist: {
                x: leftWrist.position.x,
                y: leftWrist.position.y,
                score: leftWrist.score,
            },
            rightWrist: {
                x: rightWrist.position.x,
                y: rightWrist.position.y,
                score: rightWrist.score,
            },
            leftHip: {
                x: leftHip.position.x,
                y: leftHip.position.y,
                score: leftHip.score,
            },
            rightHip: {
                x: rightHip.position.x,
                y: rightHip.position.y,
                score: rightHip.score,
            },
            leftKnee: {
                x: leftKnee.position.x,
                y: leftKnee.position.y,
                score: leftKnee.score,
            },
            rightKnee: {
                x: rightKnee.position.x,
                y: rightKnee.position.y,
                score: rightKnee.score,
            },
            leftAnkle: {
                x: leftAnkle.position.x,
                y: leftAnkle.position.y,
                score: leftAnkle.score,
            },
            rightAnkle: {
                x: rightAnkle.position.x,
                y: rightAnkle.position.y,
                score: rightAnkle.score,
            },
            image: "",
        };

        this.submitData();
    }

    drawDetections(keypoints) {
        keypoints.forEach((point) => {
            let x = this.$canvas[0].width - point.position.x;
            let y = point.position.y;

            this.ctx.beginPath();
            this.ctx.arc(x, y, 5, 0, 2 * Math.PI);
            this.ctx.fillStyle = "red";
            this.ctx.fill();
        });

        this.drawSkeleton(keypoints);
    }

    drawSkeleton(keypoints) {
        const skeleton = [
            ["leftShoulder", "rightShoulder"],
            ["leftShoulder", "leftElbow"],
            ["rightShoulder", "rightElbow"],
            ["leftElbow", "leftWrist"],
            ["rightElbow", "rightWrist"],
            ["leftShoulder", "leftHip"],
            ["rightShoulder", "rightHip"],
            ["leftHip", "rightHip"],
        ];

        skeleton.forEach(([startPartName, endPartName]) => {
            const start = keypoints.find((kp) => kp.part === startPartName);
            const end = keypoints.find((kp) => kp.part === endPartName);

            if (start && end && start.score > 0.5 && end.score > 0.5) {
                const startx = this.$canvas[0].width - start.position.x;
                const endx = this.$canvas[0].width - end.position.x;
                const starty = start.position.y;
                const endy = end.position.y;
                this.ctx.beginPath();
                this.ctx.moveTo(startx, starty);
                this.ctx.lineTo(endx, endy);
                this.ctx.strokeStyle = "blue";
                this.ctx.lineWidth = 2;
                this.ctx.stroke();
            }
        });
    }
    submitData() {
        const trialName = $("#trial_name").val();
        const gender = $('input[name="gender"]:checked').val();
        if (!trialName) {
            this.showAlert(messages.trial_name_required, "warning");
            this.isSubmitting = false;
            this.detectionLoop = true;
            return;
        }

        this.formData.trial_name = trialName;
        this.formData.gender = gender;

        console.log("Form data:", this.formData);

        $.ajax({
            url: "/trial",
            method: "POST",
            data: this.formData,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                const trialId = response.trial.id;

                // this.showAlert(measurementMessage, "success");
                $(".success-message").fadeIn();
                setTimeout(() => {
                    window.location.href = `/trial/${trialId}`;
                }, 1500);
            },
            error: (error) => {
                this.showAlert(messages.error_saving, "error");
                console.error(error);
                this.isSubmitting = false;
                this.detectionLoop = true;
            },
        });
    }

    base64ToBlob(base64) {
        const parts = base64.split(";base64,");
        const contentType = parts[0].split(":")[1];
        const raw = window.atob(parts[1]);
        const rawLength = raw.length;
        const uInt8Array = new Uint8Array(rawLength);

        for (let i = 0; i < rawLength; ++i) {
            uInt8Array[i] = raw.charCodeAt(i);
        }

        return new Blob([uInt8Array], { type: contentType });
    }
}
