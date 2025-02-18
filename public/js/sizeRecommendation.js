document.addEventListener("DOMContentLoaded", function () {
    const materialFilter = document.getElementById("material-filter");
    const styleFilter = document.getElementById("style-filter");
    const sizeCards = document.querySelectorAll(".size-card");

    function filterCards() {
        const selectedMaterial = materialFilter.value;
        const selectedStyle = styleFilter.value;

        sizeCards.forEach((card) => {
            const cardMaterial = card.dataset.material;
            const cardStyle = card.dataset.style;

            const materialMatch =
                selectedMaterial === "all" || cardMaterial === selectedMaterial;
            const styleMatch =
                selectedStyle === "all" || cardStyle === selectedStyle;

            if (materialMatch && styleMatch) {
                card.style.display = "block";
                setTimeout(() => {
                    card.style.opacity = "1";
                    card.style.transform = "translateY(0)";
                }, 50);
            } else {
                card.style.opacity = "0";
                card.style.transform = "translateY(20px)";
                setTimeout(() => {
                    card.style.display = "none";
                }, 300);
            }
        });
    }

    materialFilter.addEventListener("change", filterCards);
    styleFilter.addEventListener("change", filterCards);
});
