import "./bootstrap";
import {
    deckOpenDeleteModal,
    deckSubmitDeleteForm,
    deckOpenEditModal,
    openDownloadQRCodeModal,
    closeDownloadQRCodeModal,
} from "./deck";
import {
    reputationOpenEditModal,
    reputationOpenDeleteModal,
} from "./reputation";
import {
    cardOpenDeleteModal,
    cardSubmitDeleteForm,
    cardOpenEditModal,
    cardOpenUpdateModal,
    toggleQRCode,
    closeQRCodeModal,
} from "./card";
import { tierOpenEditModal } from "./tier";
import { validateAdminRegisterForm, showAdminDeleteModal } from "./admin";

window.deckOpenDeleteModal = deckOpenDeleteModal;
window.deckSubmitDeleteForm = deckSubmitDeleteForm;
window.deckOpenEditModal = deckOpenEditModal;
window.reputationOpenEditModal = reputationOpenEditModal;
window.reputationOpenDeleteModal = reputationOpenDeleteModal;
window.openDownloadQRCodeModal = openDownloadQRCodeModal;
window.closeDownloadQRCodeModal = closeDownloadQRCodeModal;
window.cardOpenDeleteModal = cardOpenDeleteModal;
window.cardSubmitDeleteForm = cardSubmitDeleteForm;
window.cardOpenEditModal = cardOpenEditModal;
window.cardOpenUpdateModal = cardOpenUpdateModal;
window.toggleQRCode = toggleQRCode;
window.closeQRCodeModal = closeQRCodeModal;
window.tierOpenEditModal = tierOpenEditModal;
window.validateAdminRegisterForm = validateAdminRegisterForm;
window.showAdminDeleteModal = showAdminDeleteModal;
