import PoseDetection from "./components/PoseDetection.js";

$(document).ready(function () {
    let poseDetection = null;

    function showAlert(message, type = "error") {
        $(".alert").remove();

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

        const alert = $(
            `<div class="alert alert-${type}">${icon}<span>${message}</span></div>`
        );
        $("body").append(alert);

        setTimeout(() => {
            alert.css("animation", "fadeOut 0.3s ease-out forwards");
            setTimeout(() => alert.remove(), 300);
        }, 3000);
    }

    $("#nextStep").on("click", async function () {
        const trialName = $("#trial_name").val();
        const gender = $('input[name="gender"]:checked').val();
        console.log(gender);
        if (!trialName) {
            showAlert(messages.enter_trial_name, "warning");
            return;
        }
        if (!gender || gender === "undefined") {
            showAlert(messages.select_gender, "warning");
            return;
        }

        $("#step1").hide();
        $("#step2").show();

        if (!poseDetection) {
            poseDetection = new PoseDetection();
            await poseDetection.init("video", "output");
        }
    });

    $("#trial_name").on("keypress", async function (e) {
        if (e.which === 13) {
            e.preventDefault();
            const trialName = $("#trial_name").val();
            const gender = $('input[name="gender"]:checked').val();
            if (!trialName) {
                showAlert(messages.enter_trial_name, "warning");
                return;
            }

            if (!gender || gender === "undefined") {
                showAlert(messages.select_gender, "warning");
                return;
            }

            $("#step1").hide();
            $("#step2").show();

            if (!poseDetection) {
                poseDetection = new PoseDetection();
                await poseDetection.init("video", "output");
            }
        }
    });
});
