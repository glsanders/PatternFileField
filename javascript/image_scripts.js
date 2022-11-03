// ********************
// Image Field Scripts
// ********************

let imageByteLimits = {};

function setupImageField(uid, imageByteLimit, existingAudioData) {
    imageByteLimits[uid] = imageByteLimit;
    if (existingAudioData != null && existingAudioData != "") {
        showImage(existingAudioData, uid);
    }
}

// DROP ZONE ACTIONS

function dropZoneDrop(event, uid) {
    event.preventDefault();

    hideError(uid);
    disableDropZoneHover(uid);

    let dropZone = document.getElementById(`image_drop_container_${uid}`);
    dropZone.classList.remove("hover");
    let transfer = event.dataTransfer;
    let file = transfer.files[0];
    processFile(file, uid);
}

function dropZoneDragOver(event, uid) {
    event.preventDefault();
    enableDropZoneHover(uid);
}

function onUploadFile(uid) {
    let fileInput = document.getElementById(`image_input_${uid}`);
    let file = fileInput.files[0];
    processFile(file, uid);
}

function dropZoneDragLeave(event, uid) {
    disableDropZoneHover(uid);
}

// FUNCTIONS

function enableDropZoneHover(uid) {
    let dropZone = document.getElementById(`image_drop_container_${uid}`);
    dropZone.classList.add("hover");
}

function disableDropZoneHover(uid) {
    let dropZone = document.getElementById(`image_drop_container_${uid}`);
    dropZone.classList.remove("hover");
}

function processFile(file, uid) {
    hideError(uid);
    if (!imageWithinSizeLimit(file, uid)) {
        return;
    }
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
        let base64 = reader.result;
        if (!base64.includes("image/png") && !base64.includes("image/jpeg")) {
            showError("Image must be in PNG or JPEG format.", uid);
        } else {
            showImage(base64, uid);
        }
    };
    reader.onerror = function () {
        alert(reader.error);
    };
}

function imageWithinSizeLimit(file, uid) {
    let size = file.size;
    let imageByteLimit = imageByteLimits[uid];
    let kb = imageByteLimit / 1000;
    if (size < imageByteLimit) {
        return true;
    } else {
        showError(`File must be smaller than ${kb}KB.`, uid);
    }
}

function showImage(data, uid) {
    let dropZone = document.getElementById(`image_drop_container_${uid}`);
    let imageContainer = document.getElementById(`image_container_${uid}`);
    let image = document.getElementById(`pattern_image_${uid}`);
    let valueInput = document.getElementById(`image_value_input_${uid}`);
    let fileInput = document.getElementById(`image_input_${uid}`);

    dropZone.classList.add("hidden");
    image.src = data;
    valueInput.value = data;
    imageContainer.classList.remove("hidden");
    fileInput.disabled = true;
}

function clearImage(uid) {
    let dropZone = document.getElementById(`image_drop_container_${uid}`);
    let imageContainer = document.getElementById(`image_container_${uid}`);
    let image = document.getElementById(`pattern_image_${uid}`);
    let valueInput = document.getElementById(`image_value_input_${uid}`);
    let fileInput = document.getElementById(`image_input_${uid}`);

    imageContainer.classList.add("hidden");
    image.src = "";
    dropZone.classList.remove("hidden");
    valueInput.value = "";
    fileInput.disabled = false;
}

function hideError(uid) {
    let errorElement = document.getElementById(`upload_error_${uid}`);
    errorElement.classList.add("hidden");
}

function showError(message, uid) {
    let errorElement = document.getElementById(`upload_error_${uid}`);
    errorElement.innerText = message;
    errorElement.classList.remove("hidden");
}

// ********************
// Image Preview Scripts
// ********************

function showImagePreview(uid) {
    let image = document.getElementById(`image_preview_${uid}`);
    image.src = previewImageData;
}
