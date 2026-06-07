/* ============================================================
   MediCare Plus — first.js
   Service Carousel, Doctor Carousel, Modal
   ============================================================ */

/* ── SERVICE CAROUSEL ── */
(function () {
    var serviceIdx = 0;
    function showServiceSlide(n) {
        var slides = document.querySelectorAll('.service-carousel-container .service-slide:not(.doctor-slide)');
        var dots   = document.querySelectorAll('.service-dots-container:not(.doctor-dots-container) .service-dot');
        if (!slides.length) return;
        serviceIdx = ((n % slides.length) + slides.length) % slides.length;
        slides.forEach(function(s, i) { s.style.display = i === serviceIdx ? 'block' : 'none'; });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === serviceIdx); });
    }
    window.plusServiceSlides = function(n) { showServiceSlide(serviceIdx + n); };
    window.currentServiceSlide = function(n) { showServiceSlide(n - 1); };
    document.addEventListener('DOMContentLoaded', function() { showServiceSlide(0); });
})();

/* ── DOCTOR CAROUSEL ── */
(function () {
    var doctorIdx = 0;
    function showDoctorSlide(n) {
        var slides = document.querySelectorAll('.doctor-slide');
        var dots   = document.querySelectorAll('.doctor-dot');
        if (!slides.length) return;
        doctorIdx = ((n % slides.length) + slides.length) % slides.length;
        slides.forEach(function(s, i) { s.style.display = i === doctorIdx ? 'block' : 'none'; });
        dots.forEach(function(d, i) { d.classList.toggle('active', i === doctorIdx); });
    }
    window.plusDoctorSlides = function(n) { showDoctorSlide(doctorIdx + n); };
    window.currentDoctorSlide = function(n) { showDoctorSlide(n - 1); };
    document.addEventListener('DOMContentLoaded', function() { showDoctorSlide(0); });
})();

/* ── MODAL ── */
window.openModal = function(title, content, link) {
    var modal   = document.getElementById('infoModal');
    var titleEl = document.getElementById('modalTitle');
    var bodyEl  = document.getElementById('modalContent');
    var linkEl  = document.getElementById('modalLearnMoreLink');
    if (!modal) return;
    if (titleEl) titleEl.textContent = title;
    if (bodyEl)  bodyEl.innerHTML    = content;
    if (linkEl)  linkEl.href         = link || '#';
    modal.style.display = 'flex';
    document.body.style.overflow = 'hidden';
};
window.closeModal = function() {
    var modal = document.getElementById('infoModal');
    if (modal) modal.style.display = 'none';
    document.body.style.overflow = '';
};
document.addEventListener('DOMContentLoaded', function() {
    var modal = document.getElementById('infoModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) window.closeModal();
        });
    }
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') window.closeModal();
    });
});
