// Variables

var audioPlaying = {};
let audioByteLimits = {};

// DROP ZONE ACTIONS

function audioDropZoneDrop(event, uid) {
    event.preventDefault();

    hideAudioError(uid);
    disableAudioDropZoneHover(uid);

    let dropZone = document.getElementById(`audio_drop_container_${uid}`);
    dropZone.classList.remove("hover");
    let transfer = event.dataTransfer;
    let file = transfer.files[0];
    processAudioFile(file, uid);
}

function audioDropZoneDragOver(event, uid) {
    event.preventDefault();
    enableAudioDropZoneHover(uid);
}

function onUploadAudio(uid) {
    let fileInput = document.getElementById(`audio_file_input_${uid}`);
    let file = fileInput.files[0];
    processAudioFile(file, uid);
}

function audioDropZoneDragLeave(event, uid) {
    disableAudioDropZoneHover(uid);
}

// FUNCTIONS

function setupAudioField(uid, audioByteLimit, existingAudioData) {
    audioPlaying[uid] = false;
    if (existingAudioData != null && existingAudioData != "") {
        showAudio(existingAudioData, uid);
    }
    addListeners(uid);
}

function setupPreview(uid) {
    audioPlaying[uid] = false;
    addListeners(uid);
}

function addListeners(uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    audio.addEventListener("pause", function (event) {
        onPause(event, uid);
    });

    audio.addEventListener("play", function (event) {
        onPlay(event, uid);
    });
    audio.addEventListener("timeupdate", function (event) {
        onProgress(event, uid);
    });
}

function onPause(event, uid) {
    audioPlaying[uid] = false;
    let button = document.getElementById(`audio_button_${uid}`);
    button.innerHTML = "play_circle_filled";
}

function onPlay(event, uid) {
    audioPlaying[uid] = true;
    let button = document.getElementById(`audio_button_${uid}`);
    button.innerHTML = "pause_circle_filled";
}

function onProgress(event, uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let progress = document.getElementById(`audio_progress_${uid}`);
    let percent = `${(audio.currentTime / audio.duration) * 100}%`;
    progress.style.width = percent;
}

function enableAudioDropZoneHover(uid) {
    let dropZone = document.getElementById(`audio_drop_container_${uid}`);
    dropZone.classList.add("hover");
}

function disableAudioDropZoneHover(uid) {
    let dropZone = document.getElementById(`audio_drop_container_${uid}`);
    dropZone.classList.remove("hover");
}

function processAudioFile(file, uid) {
    hideAudioError(uid);
    if (!audioWithinSizeLimit(file, uid)) {
        return;
    }
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onload = function () {
        let base64 = reader.result;
        if (!base64.includes("data:audio/mpeg;base64,")) {
            showAudioError("Must be audio file with MP3 extension.", uid);
        } else {
            showAudio(base64, uid);
        }
    };
    reader.onerror = function () {
        alert(reader.error, uid);
    };
}

function audioWithinSizeLimit(file, uid) {
    let size = file.size;
    let audioByteLimit = audioByteLimits[uid];
    let kb = audioByteLimit / 1000;
    if (size < audioByteLimit) {
        return true;
    } else {
        showAudioError(`File must be smaller than ${kb}KB.`, uid);
    }
}

function showAudio(data, uid) {
    let dropZone = document.getElementById(`audio_drop_container_${uid}`);
    let audioContainer = document.getElementById(`audio_display_container_${uid}`);
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let audio_src = document.getElementById(`pattern_audio_src_${uid}`);
    let valueInput = document.getElementById(`audio_value_input_${uid}`);
    let fileInput = document.getElementById(`audio_file_input_${uid}`);

    dropZone.classList.add("hidden");
    audio_src.src = data;
    valueInput.value = data;
    audioContainer.classList.remove("hidden");
    audio.load();
    fileInput.disabled = true;
}

function clearAudio(uid) {
    let dropZone = document.getElementById(`audio_drop_container_${uid}`);
    let audioContainer = document.getElementById(`audio_display_container_${uid}`);
    let audio = document.getElementById(`pattern_audio_${uid}`);
    let audio_src = document.getElementById(`pattern_audio_src_${uid}`);
    let valueInput = document.getElementById(`audio_value_input_${uid}`);
    let fileInput = document.getElementById(`audio_file_input_${uid}`);

    audio.pause();
    audioContainer.classList.add("hidden");
    audio_src.src = "";
    dropZone.classList.remove("hidden");
    valueInput.value = "";
    fileInput.disabled = false;
}

function hideAudioError(uid) {
    let errorElement = document.getElementById(`audio_upload_error_${uid}`);
    errorElement.classList.add("hidden");
}

function showAudioError(message, uid) {
    let errorElement = document.getElementById(`audio_upload_error_${uid}`);
    errorElement.innerText = message;
    errorElement.classList.remove("hidden");
}

function playPause(uid) {
    let audio = document.getElementById(`pattern_audio_${uid}`);
    if (audioPlaying[uid]) {
        audio.pause();
    } else {
        audio.play();
    }
}
