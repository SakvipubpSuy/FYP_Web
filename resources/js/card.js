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

export function cardOpenEditModal(card, question) {
    const form = document.getElementById("edit-form");
    form.action = `/cards/${card.card_id}/editCard`; // Route to Edit Card

    document.getElementById("edit-card-name").value = card.card_name;
    document.getElementById("edit-card-description").value =
        card.card_description;
    document.getElementById("edit-card-tier").value = card.card_tier_id;
    document.getElementById("edit-card-deck").value = card.deck_id;

    // Populate question field if it exists
    document.getElementById("edit_question").value = question.question;

    // Populate answers if they exist
    const answersContainer = document.getElementById("edit_answers");
    answersContainer.innerHTML = ""; // Clear existing answers

    if (question.answers) {
        question.answers.forEach((answer, index) => {
            let isChecked = answer.is_correct ? "checked" : "";

            // Create the same HTML structure as the one used for "Add Option"
            let answerHtml = `
            <div class="flex flex-wrap -mx-5 mb-8 w-full px-3 answer_body mt-1">
                <div class="answer">
                    <input type="text" name="edit_answers[${index}]" value="${answer.answer}" required>
                    <input type="radio" class="mr-1" name="is_correct" value="${index}" ${isChecked}> Correct
                    <button type="button" class="btn btn-danger ml-2 remove-answer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            `;
            answersContainer.insertAdjacentHTML("beforeend", answerHtml);
        });
    }

    // Remove answer option when the "X" button is clicked
    $(document).on("click", ".remove-answer", function () {
        $(this).parents("div.answer_body").remove();
    });

    var myModal = new bootstrap.Modal(document.getElementById("editModal"), {
        keyboard: false,
    });
    myModal.show();
}

export function cardOpenUpdateModal(card, question) {
    const form = document.getElementById("update-form");
    form.action = `/cards/${card.card_id}/updateCard`; //Route to Update Card

    document.getElementById("update-card-name").value = card.card_name;
    document.getElementById("update-card-description").value =
        card.card_description;
    document.getElementById("update-card-tier").value = card.card_tier_id;
    document.getElementById("update-card-deck").value = card.deck_id;

    // Populate question field if it exists
    document.getElementById("update_question").value = question.question;

    // Populate answers if they exist
    const answersContainer = document.getElementById("update_answers");
    answersContainer.innerHTML = ""; // Clear existing answers

    if (question.answers) {
        question.answers.forEach((answer, index) => {
            let isChecked = answer.is_correct ? "checked" : "";

            // Create the same HTML structure as the one used for "Add Option"
            let answerHtml = `
            <div class="flex flex-wrap -mx-5 mb-8 w-full px-3 answer_body mt-1">
                <div class="answer">
                    <input type="text" name="update_answers[${index}]" value="${answer.answer}" required>
                    <input type="radio" class="mr-1" name="is_correct" value="${index}" ${isChecked}> Correct
                    <button type="button" class="btn btn-danger ml-2 remove-answer">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>
            </div>
            `;
            answersContainer.insertAdjacentHTML("beforeend", answerHtml);
        });
    }

    // Remove answer option when the "X" button is clicked
    $(document).on("click", ".remove-answer", function () {
        $(this).parents("div.answer_body").remove();
    });

    var myModal = new bootstrap.Modal(document.getElementById("updateModal"), {
        keyboard: false,
    });
    myModal.show();
}

export function toggleQRCode(button, qrCodePath, cardName) {
    const modal = document.getElementById("qrCodeModal");
    const qrCodeImage = document.getElementById("qrCodeImage");
    const qrCodeCardName = document.getElementById("qrCodeCardName");

    // Check if modal, image, and card name exist
    if (!modal || !qrCodeImage || !qrCodeCardName) {
        console.error("Modal, QR code image, or card name not found");
        return;
    }

    // Set the QR code image source and card name
    qrCodeImage.src = qrCodePath;
    qrCodeCardName.textContent = cardName;

    // Show the modal
    modal.classList.remove("hidden");
    modal.classList.add("flex");
}

export function closeQRCodeModal() {
    const modal = document.getElementById("qrCodeModal");

    // Check if modal exists
    if (!modal) {
        console.error("Modal not found");
        return;
    }

    // Hide the modal
    modal.classList.add("hidden");
    modal.classList.remove("flex");
}
