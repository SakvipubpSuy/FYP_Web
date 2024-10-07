export function reputationOpenEditModal(reputationTitle) {
    const form = document.getElementById("edit-reputation-form");

    // Set form action route (without the ID in the URL)
    form.action = `/decks/reputation-titles/edit`; // Correct route for updating

    // Populate the form fields with the current reputation title values
    document.getElementById("edit-min-percentage").value =
        reputationTitle.min_percentage;
    document.getElementById("edit-max-percentage").value =
        reputationTitle.max_percentage;
    document.getElementById("edit-title").value = reputationTitle.title;

    // If you're sending the ID as part of the form data
    document.getElementById("edit-deck-titles-id").value =
        reputationTitle.deck_titles_id; // Hidden input field for the id

    // Show the modal
    var myModal = new bootstrap.Modal(
        document.getElementById("editReputationModal"),
        {
            keyboard: false,
        }
    );
    myModal.show();
}
export function reputationOpenDeleteModal(id, title) {
    document.getElementById("modal-deck-title-name").innerText = title;
    document.getElementById(
        "delete-reputation-form"
    ).action = `/decks/reputation-titles/${id}/delete`; // Route to delete reputation title
    let deleteModal = new bootstrap.Modal(
        document.getElementById("deleteConfirmationModal")
    );
    deleteModal.show();
}
