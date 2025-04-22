<?php
$title = "Створення спільноти";

?>
<div class="modal-overlay" id="createCommunityModal">
    <!-- Step 1 -->
    <div class="modal">
        <form id="communityForm" action="/channels/create" method="POST" enctype="multipart/form-data">
            <button type="button" class="close-btn" onclick="closeModal()">×</button>
            <div class="modal-header">
                <h2>Tell us about your community</h2>

            </div>

            <p class="modal-subtext">
                A name and description help people understand what your community is all about.
            </p>

            <div class="preview-card">
                <div style="margin-left: 5px;">
                    <strong style="font-size: 1.2rem;">p/<span id="previewName">communityname</span></strong><br />
                    <span style="font-size: 0.8rem;">1 member · 1 online</span>
                </div>

                <small id="previewDesc" style="font-size: 0.8rem;">Your community description</small>
            </div>

            <div class="step step-1 active">
                <label>Community name <span style="color: red;">*</span></label>
                <input type="text" id="communityName" name="name" maxlength="21" required
                    oninput="updatePreview(); updateCharCount('communityName', 21); validateNameInput()" />
                <small id="nameCounter" class="char-counter">21 characters left</small>
                <p id="nameError" style="color: red; font-size: 0.9rem; display: none;">
                    Назва може містити лише літери та цифри.
                </p>

                <label>Description <span style="color: red;">*</span></label>
                <textarea id="communityDesc" name="description" maxlength="500" required
                    oninput="updatePreview(); updateCharCount('communityDesc', 500)"></textarea>
                <small id="descCounter" class="char-counter">500 characters left</small>
            </div>

            <!-- Step 2 -->
            <div class="step step-2 hidden">
                <label>Upload Icon:</label>
                <input type="file" name="icon" accept="image/*" id="iconUpload" />
                <label>Upload Banner:</label>
                <input type="file" name="banner" accept="image/*" id="bannerUpload" />
            </div>

            <!-- Step 3 -->
            <div class="step step-3 hidden">
                <label>Hashtags:</label>
                <input type="text" id="hashtags" name="hashtags" placeholder="#programming #php" />
            </div>

            <!-- Step 4 -->
            <div class="step step-4 hidden">
                <label>Privacy:</label>
                <select id="privacy" name="privacy">
                    <option value="private">Private (invite only)</option>
                    <option value="readonly">Read-only for guests</option>
                    <option value="public">Public (anyone can join & post)</option>
                </select>

                <label>
                    <input type="checkbox" id="nsfw" name="is_18" />
                    18+ Content
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="prev-btn" onclick="changeStep(-1)" disabled>
                    Back
                </button>

                <!-- Кнопка Next: завжди type=button -->
                <button id="nextBtn" type="button" class="next-btn" onclick="changeStep(1)">
                    Next
                </button>

                <!-- Кнопка Submit: завжди type=submit, спочатку схована -->
                <button id="submitBtn" type="submit" class="submit-btn" style="display:none">
                    Create
                </button>
            </div>
        </form>

    </div>

</div>
<script>
    let currentStep = 1;
    // const modalform = document.querySelector('#communityForm');

    function updatePreview() {
        const name = document.getElementById("communityName").value.trim();
        const desc = document.getElementById("communityDesc").value.trim();

        document.getElementById("previewName").textContent = name || "communityname";
        document.getElementById("previewDesc").textContent = desc || "Your community description";
    }

    function showCreateChannel() {
        const modalform = document.querySelector('#createCommunityModal');
        modalform.style.display = 'block';
        currentStep = 1;
        updateSteps(); // опційно: оновлення активного етапу
    }

    function closeModal() {
        const modal = document.querySelector('#createCommunityModal');
        const form = document.getElementById('communityForm');

        // form.reset();

        // updateCharCount('communityName', 21);
        // updateCharCount('communityDesc', 500);

        // document.getElementById('nameError').style.display = 'none';

        currentStep = 1;

        document.querySelector(".prev-btn").disabled = true;
        document.getElementById('nextBtn').style.display = 'inline-block';
        document.getElementById('submitBtn').style.display = 'none';

        updateSteps();

        modal.style.display = 'none';
    }

    function updateCharCount(fieldId, maxLength) {
        const input = document.getElementById(fieldId);
        const counterId = fieldId === "communityName" ? "nameCounter" : "descCounter";
        const counter = document.getElementById(counterId);
        const remaining = maxLength - input.value.length;
        counter.textContent = `${remaining} characters left`;
    }

    function updateSteps() {
        document.querySelectorAll('.step').forEach(step => step.classList.remove('active'));
        document.querySelector(`.step-${currentStep}`)?.classList.add('active');
    }

    function changeStep(direction) {
        if (direction === 1 && !validateCurrentStep()) return;

        // ховаємо поточний крок, показуємо наступний
        document.querySelector(`.step-${currentStep}`)?.classList.remove('active');
        currentStep += direction;
        document.querySelector(`.step-${currentStep}`)?.classList.add('active');

        // оновлюємо кнопки
        document.querySelector(".prev-btn").disabled = currentStep === 1;
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');

        if (currentStep === 4) {
            nextBtn.style.display = 'none';
            submitBtn.style.display = 'inline-block';
        } else {
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
        }
    }

    function validateNameInput() {
        if (currentStep === 1) {
            const input = document.getElementById("communityName");
            const error = document.getElementById("nameError");
            // Дозволяємо тільки A–Z, a–z, 0–9 (порожній рядок теж ок)
            const isValid = /^[A-Za-z0-9_]*$/.test(input.value);
            if (isValid) {
                error.style.display = 'none';
            } else {
                error.style.display = 'block';
            }
            return isValid;
        }
    }

    function validateCurrentStep() {
        if (currentStep === 1) {
            const name = document.getElementById("communityName").value.trim();
            const desc = document.getElementById("communityDesc").value.trim();

            if (!validateNameInput()) {
                return false;

                if (!name || !desc) {
                    alert("Будь ласка, заповніть назву та опис спільноти.");
                    return false;
                }
            }

            // if (currentStep === 2) {
            //     const icon = document.getElementById("iconUpload").files[0];
            //     const banner = document.getElementById("bannerUpload").files[0];

            //     if (!icon || !banner) {
            //         alert("Будь ласка, завантажте іконку та банер.");
            //         return false;
            //     }
            // }
        }
        return true;
    }
</script>