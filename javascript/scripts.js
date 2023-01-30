var fetchedLibraryItems = {};

function configureField(uid) {
    addAudioListeners(uid);
    // fetchAllLibraryItems(uid);
}

async function query(url, callback) {
    response = await fetch(url);
    if (response.ok) {
        try {
            let responseJson = await response.json();
            if (callback !== "") {
                callback(responseJson);
            } else {
                console.log(responseJson);
            }
        } catch (err) {
            console.log(err);
            console.log(response.toString());
        }
    } else {
        console.log("Status Code: " + response.status);
        console.log("Status Message: " + response.statusText);
    }
}

// DROP ZONE ACTIONS

function dropZoneDrop(event, uid) {
    event.preventDefault();

    hideError(uid);
    dropZoneDragLeave(event, uid);

    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    dropZone.classList.remove("hover");
    let transfer = event.dataTransfer;
    let file = transfer.files[0];
    processFile(file, uid);
}

function dropZoneDragOver(event, uid) {
    event.preventDefault();
    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    dropZone.classList.add("hover");
}

function onUploadFile(uid) {
    let fileInput = document.getElementById(`file_input_${uid}`);
    let file = fileInput.files[0];
    processFile(file, uid);
}

function dropZoneDragLeave(event, uid) {
    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    dropZone.classList.remove("hover");
}

// FUNCTIONS

function processFile(file, uid) {
    hideError(uid);

    if (!withinSizeLimit(file, uid)) return;
    if (!validFileType(file, uid)) return;

    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
        let base64 = reader.result;
        let uploadedFile = {
            type: "upload",
            name: file.name,
            data: base64,
            mimeType: file.type,
        };
        if (uploadedFile.data.includes("image/")) {
            showImage(uploadedFile, uid);
        } else if (uploadedFile.data.includes("audio/")) {
            showAudio(uploadedFile, uid);
        } else if (uploadedFile.data.includes("video/")) {
            showVideo(uploadedFile, uid);
        } else {
            showFile(uploadedFile, uid);
        }
    };
    reader.onerror = function () {
        alert(reader.error);
    };
}

function validFileType(file, uid) {
    let wrapper = document.getElementById(`pattern_file_field_wrapper_${uid}`);
    let typeListRaw = wrapper.dataset.allowedTypes;
    let typeList = typeListRaw.split(",");
    if (typeList.length == 0 || typeList[0] == "") {
        return true;
    }
    let type = file.type;
    if (!typeList.includes(type)) {
        showError("Invalid file format.", uid);
        return false;
    }
    return true;
}

function withinSizeLimit(file, uid) {
    let size = file.size;
    let wrapper = document.getElementById(`pattern_file_field_wrapper_${uid}`);
    let byteLimit = wrapper.dataset.byteLimit;
    let kb = byteLimit / 1000;
    if (size < byteLimit) {
        return true;
    } else {
        showError(`File must be smaller than ${kb}KB.`, uid);
        return false;
    }
}

function getByteLimit(uid) {
    let wrapper = document.getElementById(`pattern_file_field_wrapper_${uid}`);
    let byteLimit = wrapper.dataset.byeLimit;
    return byteLimit;
}

function showImage(imageObject, uid) {
    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    let imageContainer = document.getElementById(`image_container_${uid}`);
    let image = document.getElementById(`pattern_image_${uid}`);
    let valueInput = document.getElementById(`value_input_${uid}`);
    let fileInput = document.getElementById(`file_input_${uid}`);

    dropZone.classList.add("hidden");
    image.src = imageObject.data;
    valueInput.value = JSON.stringify(imageObject);
    imageContainer.classList.remove("hidden");
    fileInput.disabled = true;
}

function showVideo(videoObject, uid) {
    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    let videoContainer = document.getElementById(`video_container_${uid}`);
    let video = document.getElementById(`pattern_video_${uid}`);
    let videoSrc = document.getElementById(`pattern_video_src_${uid}`);
    let valueInput = document.getElementById(`value_input_${uid}`);
    let fileInput = document.getElementById(`file_input_${uid}`);

    dropZone.classList.add("hidden");
    videoSrc.type = videoObject.mimeType;
    videoSrc.src = videoObject.data;
    valueInput.value = JSON.stringify(videoObject);
    videoContainer.classList.remove("hidden");
    video.load();
    fileInput.disabled = true;
}

function showAudio(audioObject, uid) {
    let dropZone = document.getElementById(`file_drop_container_${uid}`);
    let audioContainer = document.getElementById(`audio_container_${uid}`);
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let audio_src = document.getElementById(`pattern_audio_src_${uid}`);
    let valueInput = document.getElementById(`value_input_${uid}`);
    let fileInput = document.getElementById(`file_input_${uid}`);

    dropZone.classList.add("hidden");
    audio_src.src = audioObject.data;
    valueInput.value = JSON.stringify(audioObject);
    audioContainer.classList.remove("hidden");
    audio.load();
    fileInput.disabled = true;
}

function showFile(fileObject, uid) {}

function clearFile(uid) {
    let dropZone = document.getElementById(`file_drop_container_${uid}`);

    let imageContainer = document.getElementById(`image_container_${uid}`);
    let image = document.getElementById(`pattern_image_${uid}`);

    let audioContainer = document.getElementById(`audio_container_${uid}`);
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let audioSrc = document.getElementById(`pattern_audio_src_${uid}`);

    let videoContainer = document.getElementById(`video_container_${uid}`);
    let video = document.getElementById(`pattern_video_${uid}`);
    let videoSrc = document.getElementById(`pattern_video_src_${uid}`);

    let valueInput = document.getElementById(`value_input_${uid}`);
    let fileInput = document.getElementById(`file_input_${uid}`);

    imageContainer.classList.add("hidden");
    audioContainer.classList.add("hidden");
    videoContainer.classList.add("hidden");
    image.src = "";
    audio.pause();
    audioSrc.src = "";
    videoSrc.src = "";
    videoSrc.type = "";
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

// Audio Scripts

function playPause(uid) {
    console.log("Button: Play/Pause");
    let audio = document.getElementById(`pattern_audio_${uid}`);
    if (getPlayStatus(uid)) {
        console.log("Playing");
        audio.pause();
    } else {
        console.log("Not Playing");
        audio.play();
    }
}

function onAudioProgress(event, uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let progress = document.getElementById(`audio_progress_${uid}`);
    let percent = `${(audio.currentTime / audio.duration) * 100}%`;
    progress.style.width = percent;
}

function setupPreview(uid) {
    setPlayStatus(false, uid);
    addAudioListeners(uid);
}

function addAudioListeners(uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    console.log("Listener: Pause");
    audio.addEventListener("pause", function (event) {
        onPause(event, uid);
    });

    audio.addEventListener("play", function (event) {
        console.log("Listener: Play");
        onPlay(event, uid);
    });
    audio.addEventListener("timeupdate", function (event) {
        console.log("Listener: Progress");
        onProgress(event, uid);
    });
}

function onPlay(event, uid) {
    setPlayStatus(true, uid);
    let button = document.getElementById(`audio_button_${uid}`);
    button.innerHTML = "pause_circle_filled";
}

function onPause(event, uid) {
    setPlayStatus(false, uid);
    let button = document.getElementById(`audio_button_${uid}`);
    button.innerHTML = "play_circle_filled";
}

function onProgress(event, uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let progress = document.getElementById(`audio_progress_${uid}`);
    let percent = `${(audio.currentTime / audio.duration) * 100}%`;
    progress.style.width = percent;
}

function setPlayStatus(playing, uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    if (playing) {
        audio.dataset.playing = "";
    } else {
        audio.removeAttribute("data-playing");
    }
}

function getPlayStatus(uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let playing = audio.dataset.playing;
    return playing != null;
}

// Library Scripts

function showLibrary(uid) {
    var modal = document.getElementById(`libraryNewModal_${uid}`);
    modal.style.display = "block";
}

function closeLibrary(uid) {
    clearSelection(uid);
    var modal = document.getElementById(`libraryNewModal_${uid}`);
    modal.style.display = "none";
}

function selectLibraryEntry(entry_id, uid) {
    clearSelection(uid);
    let newSelected = document.getElementById(`library_preview_wrapper_${entry_id}_${uid}`);
    newSelected.classList.add("selected");

    let applyButton = document.getElementById(`library_apply_button_${uid}`);
    applyButton.disabled = false;
}

function applyFromLibrary(uid) {
    let currentSelected = document.getElementsByClassName("library_preview_wrapper selected")[0];
    if (currentSelected && currentSelected.dataset.fieldId == uid) {
        currentSelected.classList.remove("selected");
        let entryID = currentSelected.dataset.entryId;
        let imageTag = document.getElementById(`image_preview_${entryID}_${uid}`);
        let file = {
            type: "library",
            id: entryID,
            data: imageTag.src,
        };
        showImage(file, uid);
    }
    closeLibrary(uid);
}

function clearSelection(uid) {
    let currentSelected = document.getElementsByClassName("library_preview_wrapper selected")[0];
    if (currentSelected) {
        currentSelected.classList.remove("selected");
    }

    let applyButton = document.getElementById(`library_apply_button_${uid}`);
    applyButton.disabled = true;
}

function fetchEntryData(entry_id, uid) {
    var req = new XMLHttpRequest();
    req.onreadystatechange = function () {
        if (xhr.readyState === 4) {
            var response = JSON.parse(req.responseText);
            console.log(response);
        }
    };

    req.open("GET", "http://nikola-breznjak.com/_testings/ajax/test2.php");
    req.send();
}

function fetchAllLibraryItems(uid) {
    let wrappers = document.getElementsByClassName("library_preview_wrapper");
    for (const wrapper of wrappers) {
        let libraryId = wrapper.dataset.entryId;
        let url = wrapper.dataset.fetchUrl;
        let image = document.getElementById(`image_preview_${libraryId}_${uid}`);
        if (libraryId in fetchedLibraryItems) {
            image.src = fetchAllLibraryItems[libraryId];
        } else {
            query(url, function (responseJSON) {
                let data = responseJSON["payload"][0]["entry_value"];
                image.src = data;
            });
        }
    }
}

// Preview Scripts

function showImagePreview(uid) {
    let image = document.getElementById(`image_preview_${uid}`);
    image.src = previewImageData;
}
