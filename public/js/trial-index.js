function showDeleteModal(trialId) {
    console.log("showDeleteModal called with trialId:", trialId);
    const modal = document.getElementById("deleteModal");
    const backdrop = document.getElementById("modalBackdrop");
    const deleteForm = document.getElementById("deleteForm");

    deleteForm.action = `/trial/${trialId}`;
    modal.style.display = "block";
    backdrop.style.display = "block";

    modal.style.opacity = "0";
    modal.style.transform = "translate(-50%, -60%)";
    backdrop.style.opacity = "0";

    requestAnimationFrame(() => {
        modal.style.transition = "all 0.3s ease-out";
        backdrop.style.transition = "opacity 0.3s ease-out";
        modal.style.opacity = "1";
        modal.style.transform = "translate(-50%, -50%)";
        backdrop.style.opacity = "1";
    });
}

function hideDeleteModal() {
    const modal = document.getElementById("deleteModal");
    const backdrop = document.getElementById("modalBackdrop");

    modal.style.opacity = "0";
    modal.style.transform = "translate(-50%, -60%)";
    backdrop.style.opacity = "0";

    setTimeout(() => {
        modal.style.display = "none";
        backdrop.style.display = "none";
        modal.style.transform = "translate(-50%, -50%)";
    }, 300);
}

document
    .getElementById("modalBackdrop")
    .addEventListener("click", hideDeleteModal);

const alert = document.querySelector(".alert");

if (alert) {
    setTimeout(() => {
        alert.style.transition = "opacity 0.3s ease-out";
        alert.style.opacity = "0";
        setTimeout(() => {
            alert.style.display = "none";
        }, 300);
    }, 2000);
}
