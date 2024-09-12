export function validateAdminRegisterForm() {
    // Email validation
    const emailInput = document.getElementById("email");
    const emailError = document.getElementById("emailError");
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(emailInput.value)) {
        emailInput.classList.add("is-invalid");
        emailError.style.display = "block";
        return false; // Prevent form submission
    } else {
        emailInput.classList.remove("is-invalid");
        emailError.style.display = "none";
    }

    // Password match validation
    const passwordInput = document.getElementById("password");
    const passwordConfirmInput = document.getElementById(
        "password_confirmation"
    );
    const passwordError = document.getElementById("passwordError");
    if (passwordInput.value !== passwordConfirmInput.value) {
        passwordConfirmInput.classList.add("is-invalid");
        passwordError.style.display = "block";
        return false; // Prevent form submission
    } else {
        passwordConfirmInput.classList.remove("is-invalid");
        passwordError.style.display = "none";
    }

    return true; // Allow form submission if there are no errors
}

export function showAdminDeleteModal(adminId, adminName) {
    document.getElementById("adminNameToDelete").textContent = adminName;
    document.getElementById("deleteAdminForm").action = `/admins/${adminId}`;
    var deleteModal = new bootstrap.Modal(
        document.getElementById("confirmDeleteModal")
    );
    deleteModal.show();
}
