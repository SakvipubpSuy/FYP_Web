export function deckOpenDeleteModal(deckId, page) {
    document.getElementById("delete-deck-id").value = deckId;
    var deleteForm = document.getElementById("delete-form");
    deleteForm.action = "/decks/" + deckId;
    document.getElementById("delete-form").elements.namedItem("page").value =
        page;
    var deleteModal = new bootstrap.Modal(
        document.getElementById("deleteModal")
    );
    deleteModal.show();
}
export function deckSubmitDeleteForm() {
    document.getElementById("delete-form").submit();
}
export function deckOpenEditModal(deck) {
    const form = document.getElementById("edit-form");
    form.action = `/decks/${deck.deck_id}/editDeck`; // Route to Edit Card
    document.getElementById("edit-deck-name").value = deck.deck_name;
    document.getElementById("edit-deck-description").value =
        deck.deck_description;
    var myModal = new bootstrap.Modal(document.getElementById("editModal"), {
        keyboard: false,
    });
    myModal.show();
}
