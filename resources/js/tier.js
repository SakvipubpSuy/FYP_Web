export function tierOpenEditModal(tier) {
    const form = document.getElementById("edit-tier-form");
    form.action = `/tiers/${tier.card_tier_id}/editTier`; // Route to update card tier

    document.getElementById("edit-card-tier-name").value = tier.card_tier_name;
    document.getElementById("edit-card-XP").value = tier.card_XP;
    document.getElementById("edit-card-energy-required").value =
        tier.card_energy_required;
    document.getElementById("edit-color").value = tier.color;

    var myModal = new bootstrap.Modal(
        document.getElementById("editTierModal"),
        {
            keyboard: false,
        }
    );
    myModal.show();
}

document.addEventListener("DOMContentLoaded", function () {
    const initPickr = (elementId, inputId, defaultColor) => {
        const pickr = Pickr.create({
            el: elementId,
            theme: "classic", // or 'monolith', or 'nano'
            default: defaultColor,
            swatches: [
                "rgba(244, 67, 54, 1)",
                "rgba(233, 30, 99, 1)",
                "rgba(156, 39, 176, 1)",
                "rgba(103, 58, 183, 1)",
                "rgba(63, 81, 181, 1)",
                "rgba(33, 150, 243, 1)",
                "rgba(3, 169, 244, 1)",
                "rgba(0, 188, 212, 1)",
                "rgba(0, 150, 136, 1)",
                "rgba(76, 175, 80, 1)",
                "rgba(139, 195, 74, 1)",
                "rgba(205, 220, 57, 1)",
                "rgba(255, 235, 59, 1)",
                "rgba(255, 193, 7, 1)",
            ],
            components: {
                // Main components
                preview: true,
                opacity: true,
                hue: true,

                // Input / output Options
                interaction: {
                    hex: true,
                    rgba: true,
                    input: true,
                    save: true,
                },
            },
        });

        pickr.on("save", (color, instance) => {
            document.getElementById(inputId).value = color.toHEXA().toString();
            pickr.hide();
        });
    };

    // Initialize color picker for create form
    if (document.getElementById("create-color-picker")) {
        initPickr("#create-color-picker", "create-color", "#000000");
    }

    // Initialize color picker for edit form
    if (document.getElementById("edit-color-picker")) {
        const editColor =
            document.getElementById("edit-color").value || "#000000";
        initPickr("#edit-color-picker", "edit-color", editColor);
    }
});
