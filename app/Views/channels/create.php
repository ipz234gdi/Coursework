<?php
$title = "Створення спільноти";

?>
<div class="modal-overlay" id="createCommunityModal">
    <!-- Step 1 -->
    <div class="modal">
        <form id="communityForm">
            <button class="close-btn" onclick="closeModal()">×</button>
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
                <input type="text" id="communityName" maxlength="21" required
                    oninput="updatePreview(); updateCharCount('communityName', 21)" />
                <small id="nameCounter" class="char-counter">21 characters left</small>

                <label>Description <span style="color: red;">*</span></label>
                <textarea id="communityDesc" maxlength="500" required
                    oninput="updatePreview(); updateCharCount('communityDesc', 500)"></textarea>
                <small id="descCounter" class="char-counter">500 characters left</small>
            </div>

            <!-- Step 2 -->
            <div class="step step-2 hidden">
                <label>Upload Icon:</label>
                <input type="file" accept="image/*" id="iconUpload" />
                <label>Upload Banner:</label>
                <input type="file" accept="image/*" id="bannerUpload" />
            </div>

            <!-- Step 3 -->
            <div class="step step-3 hidden">
                <label>Hashtags:</label>
                <input type="text" id="hashtags" placeholder="#programming #php" />
            </div>

            <!-- Step 4 -->
            <div class="step step-4 hidden">
                <label>Privacy:</label>
                <select id="privacy">
                    <option value="private">Private (invite only)</option>
                    <option value="readonly">Read-only for guests</option>
                    <option value="public">Public (anyone can join & post)</option>
                </select>

                <label>
                    <input type="checkbox" id="nsfw" />
                    18+ Content
                </label>
            </div>

            <div class="modal-footer">
                <button type="button" class="prev-btn" onclick="changeStep(-1)" disabled>Back</button>
                <button type="button" class="next-btn" onclick="changeStep(1)">Next</button>
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
        const modalform = document.querySelector('#createCommunityModal');
        modalform.style.display = 'none';
        currentStep = 1;
        updateSteps(); // опційно: оновлення активного етапу
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

        document.querySelector(`.step-${currentStep}`)?.classList.remove('active');
        currentStep += direction;
        document.querySelector(`.step-${currentStep}`)?.classList.add('active');

        // Кнопки
        document.querySelector(".prev-btn").disabled = currentStep === 1;
        document.querySelector(".next-btn").textContent =
            currentStep === 4 ? "Submit" : "Next";

        if (currentStep === 4) {
            document.querySelector(".next-btn").onclick = submitForm;
        } else {
            document.querySelector(".next-btn").onclick = () => changeStep(1);
        }
    }

    function validateCurrentStep() {
        if (currentStep === 1) {
            const name = document.getElementById("communityName").value.trim();
            const desc = document.getElementById("communityDesc").value.trim();

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

        return true;
    }

    function submitForm() {
        const formData = new FormData();

        formData.append("name", document.getElementById("communityName").value);
        formData.append("description", document.getElementById("communityDesc").value);
        formData.append("icon", document.getElementById("iconUpload").files[0]);
        formData.append("banner", document.getElementById("bannerUpload").files[0]);
        formData.append("hashtags", document.getElementById("hashtags").value);
        formData.append("privacy", document.getElementById("privacy").value);
        formData.append("nsfw", document.getElementById("nsfw").checked ? 1 : 0);

        // TODO: fetch('/api/channels/create', { method: 'POST', body: formData });

        console.log("Submitting...", Object.fromEntries(formData.entries()));
        closeModal();
    }
</script>