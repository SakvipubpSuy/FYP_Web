export function cardOpenDeleteModal(cardId, page) {
    document.getElementById("delete-card-id").value = cardId;
    var deleteForm = document.getElementById("delete-form");
    deleteForm.action = "/cards/" + cardId;
    document.getElementById("delete-form").elements.namedItem("page").value =
        page;
    var deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );
    deleteModal.show();
}

export function cardSubmitDeleteForm() {
    document.getElementById("delete-form").submit();
}

export function cardOpenEditModal(card) {
    const form = document.getElementById("edit-form");
    form.action = `/cards/${card.card_id}/editCard`; // Route to Edit Card

    document.getElementById("edit-card-name").value = card.card_name;
    document.getElementById("edit-card-description").value =
        card.card_description;
    document.getElementById("edit-card-tier").value = card.card_tier_id;
    document.getElementById("edit-card-deck").value = card.deck_id;

    var myModal = new bootstrap.Modal(document.getElementById("editModal"), {
        keyboard: false,
    });
    myModal.show();
}
export function cardOpenUpdateModal(card) {
    const form = document.getElementById("update-form");
    form.action = `/cards/${card.card_id}/updateCard`; //Route to Update Card

    document.getElementById("update-card-name").value = card.card_name;
    document.getElementById("update-card-description").value =
        card.card_description;
    document.getElementById("update-card-tier").value = card.card_tier_id;
    document.getElementById("update-card-deck").value = card.deck_id;

    var myModal = new bootstrap.Modal(document.getElementById("updateModal"), {
        keyboard: false,
    });
    myModal.show();
}
