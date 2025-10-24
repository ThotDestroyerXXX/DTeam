/**
 * Gift Checkout functionality
 * Handles the gift recipient selection and checkout process
 */

// Store selected recipients in memory until checkout
let selectedRecipients = {};
let giftMessages = {};

// Keep track of gift items that need recipients
let giftItemsRequiringRecipients = [];

/**
 * Initialize on page load
 */
document.addEventListener("DOMContentLoaded", function () {
    // Get current user information
    const currentUserName = document.getElementById("current-user-name").value;
    const currentUserPictureUrl = document.getElementById(
        "current-user-picture"
    ).value;

    // Find all gift items that need recipients
    document.querySelectorAll(".gift-recipient-section").forEach((section) => {
        const cartId = section.id.replace("gift-recipient-section-", "");
        if (!section.querySelector(".selected-recipient")) {
            giftItemsRequiringRecipients.push(cartId);
        } else {
            // If there's already a recipient selected (from previous page load)
            const recipientId = section.dataset.recipientId;
            const recipientName = section.dataset.recipientName;
            if (recipientId && recipientName) {
                selectedRecipients[cartId] = {
                    id: recipientId,
                    name: recipientName,
                };
            }
        }
    });

    // Update checkout button state
    updateCheckoutButtonState();
});

/**
 * Select a gift recipient
 * @param {string} cartId - The ID of the cart item
 * @param {string} friendId - The ID of the friend
 * @param {string} friendName - The name of the friend
 * @param {string} profilePictureUrl - The profile picture URL of the friend
 */
function selectRecipient(cartId, friendId, friendName, profilePictureUrl) {
    // Get current user information
    const currentUserName = document.getElementById("current-user-name").value;
    const currentUserPictureUrl = document.getElementById(
        "current-user-picture"
    ).value;

    // Store the selected recipient for this cart item
    selectedRecipients[cartId] = {
        id: friendId,
        name: friendName,
        profilePicture: profilePictureUrl,
    };

    // Remove this cart from the requiring recipients list
    const index = giftItemsRequiringRecipients.indexOf(cartId);
    if (index > -1) {
        giftItemsRequiringRecipients.splice(index, 1);
    }

    // Update checkout button state
    updateCheckoutButtonState();

    // Close the modal
    document.getElementById(`select-recipient-modal-${cartId}`).close();

    // Update the UI to show the selected recipient
    const recipientSection = document.getElementById(
        `gift-recipient-section-${cartId}`
    );

    recipientSection.innerHTML = `
        <div class="selected-recipient">
            <div class="divider m-0 p-0"></div>
            <div class="flex items-center justify-between text-sm">
                <a href="{{ route('user.profile.index', $friend->id) }}" class="flex items-center gap-1">
                    <span class="font-medium">Gift Recipient:</span>
                    <span class="flex flex-row gap-1 items-center"><img src="${profilePictureUrl}"
                            alt="${friendName}'s avatar"
                            class="w-4 h-4 rounded-full inline-block" />${friendName}</span>
                </a>
                <button class="btn btn-link btn-sm"
                    onclick="document.getElementById('select-recipient-modal-${cartId}').showModal()">
                    Edit
                </button>
            </div>

            <div class="mb-2 text-sm">
                <span class="font-medium block mb-1">Gift Message:</span>
                <textarea class="textarea textarea-bordered w-full"
                    id="gift-message-${cartId}"
                    oninput="updateGiftMessage('${cartId}')"
                    placeholder="Add a personal message to your gift">${
                        giftMessages[cartId] || ""
                    }</textarea>
            </div>

            <div class="flex items-center text-sm">
                <span class="font-medium mr-1">From:</span>
                <div class="avatar mr-1">
                    <div class="w-6 h-6 rounded-full">
                        <img src="${currentUserPictureUrl}" alt="Your avatar" />
                    </div>
                </div>
                <span>${currentUserName}</span>
            </div>
        </div>
    `;
}

/**
 * Update gift message when the user types in the textarea
 * @param {string} cartId - The ID of the cart item
 */
function updateGiftMessage(cartId) {
    const messageElement = document.getElementById(`gift-message-${cartId}`);
    giftMessages[cartId] = messageElement.value;
}

/**
 * Update checkout button enabled/disabled state based on whether all gifts have recipients
 */
function updateCheckoutButtonState() {
    const checkoutButton = document.getElementById("checkout-button");
    if (!checkoutButton) return;

    // Check if all gift items have recipients
    if (giftItemsRequiringRecipients.length > 0) {
        checkoutButton.disabled = true;
        checkoutButton.title =
            "Please select a recipient for all gifts before checkout";
    } else {
        checkoutButton.disabled = false;
        checkoutButton.title = "";
    }
}

/**
 * Prepare gift details for submission
 * @returns {boolean} - Whether the form should be submitted
 */
function prepareGiftDetails() {
    // Double-check that all gifts have recipients
    if (giftItemsRequiringRecipients.length > 0) {
        alert("Please select a recipient for all gift items before checkout.");
        return false;
    }

    const giftDetails = [];

    Object.keys(selectedRecipients).forEach((cartId) => {
        giftDetails.push({
            cart_id: cartId,
            recipient_id: selectedRecipients[cartId].id,
            message: giftMessages[cartId] || "",
        });
    });

    // Store in hidden input
    document.getElementById("gift-details").value = JSON.stringify(giftDetails);

    // Submit the form
    return true;
}
