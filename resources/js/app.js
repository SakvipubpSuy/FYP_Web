import "./bootstrap";
import {
    deckOpenDeleteModal,
    deckSubmitDeleteForm,
    deckOpenEditModal,
} from "./deck";
import {
    cardOpenDeleteModal,
    cardSubmitDeleteForm,
    cardOpenEditModal,
    cardOpenUpdateModal,
    showQRCode,
} from "./card";

window.deckOpenDeleteModal = deckOpenDeleteModal;
window.deckSubmitDeleteForm = deckSubmitDeleteForm;
window.deckOpenEditModal = deckOpenEditModal;
window.cardOpenDeleteModal = cardOpenDeleteModal;
window.cardSubmitDeleteForm = cardSubmitDeleteForm;
window.cardOpenEditModal = cardOpenEditModal;
window.cardOpenUpdateModal = cardOpenUpdateModal;
window.showQRCode = showQRCode;
