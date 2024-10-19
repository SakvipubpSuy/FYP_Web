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
    const downloadButton = document.getElementById("downloadQRCode");

    // Check if modal, image, and card name exist
    if (!modal || !qrCodeImage || !qrCodeCardName) {
        console.error("Modal, QR code image, or card name not found");
        return;
    }

    // Set the QR code image source and card name
    qrCodeImage.src = qrCodePath;
    qrCodeCardName.textContent = cardName;

    // Set the download link for the QR code
    downloadButton.href = qrCodePath;
    // Create a file name for the download (replace spaces with underscores)
    const sanitizedCardName = cardName.replace(/\s+/g, "_");
    downloadButton.download = `${sanitizedCardName}_QR_Code.png`; // Set the download attribute for the file name

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
export function openCardPreview(imageUrl, cardName, tierColor, deckName, energy, cardTierName, exp, version) {
    const modalHtml = `
        <div id="cardPreviewModal" class="fixed inset-0 flex justify-center items-center bg-black bg-opacity-50 z-50">
            <div class="bg-white p-4 rounded-lg shadow-lg w-80">
                <!-- Card Border with dynamic gradient background -->
                <div class="border-4 rounded-lg overflow-hidden shadow-md" style="border-color: ${tierColor}; background: linear-gradient(to top, rgba(0, 0, 0, 0.9), ${tierColor}, rgba(0, 0, 0, 0.9));">
                    <!-- Card Details -->
                    <div class="text-left text-gray-800 p-4 relative">
                        <!-- Deck Name and Energy -->
                        <span class="absolute top-2 left-2 text-sm font-bold " style="color: ${tierColor};">${deckName}</span>
                        <span class="absolute top-2 right-2 text-sm flex items-center" style="color: ${tierColor};">
                            <i class="fas fa-bolt"></i> ${energy}
                        </span>
                    </div>
                    <!-- Card Image -->
                    <div class="p-4">
                        <img class="mx-auto h-32 w-48 object-cover" src="${imageUrl}" alt="Card Image">
                    </div>
                    <!-- Card Info -->
                    <div class="text-center text-white px-4 py-2">
                        <h3 class="text-lg font-bold text-white-800">${cardName}</h3>
                    </div>
                    <div class="text-left text-white px-4 py-2">
                        <p>Card Tier: ${cardTierName}</p>
                        <p>EXP: ${exp}</p>
                        <p>Version: ${version}</p>
                    </div>
                </div>
                <!-- Close Button -->
                <div class="text-center p-4">
                    <button class="bg-red-500 text-white py-2 px-4 rounded" onclick="closeCardPreview()">Close</button>
                </div>
            </div>
        </div>
    `;
    
    // Append modal to the body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

export function closeCardPreview() {
    const modal = document.getElementById('cardPreviewModal');
    if (modal) {
        modal.remove();
    }
}