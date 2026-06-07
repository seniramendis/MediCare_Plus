
<footer class="site-footer">

    <!-- Newsletter Bar -->
    <div class="footer-newsletter">
        <div class="newsletter-inner">
            <div class="newsletter-text">
                <i class="fas fa-envelope-open-text"></i>
                <div>
                    <strong>Stay Healthy — Stay Informed</strong>
                    <p>Get expert health tips and MediCare Plus updates straight to your inbox.</p>
                </div>
            </div>
            <form class="newsletter-form" onsubmit="handleNewsletter(event)">
                <input type="email" placeholder="Enter your email address..." required>
                <button type="submit"><i class="fas fa-paper-plane"></i> Subscribe</button>
            </form>
        </div>
    </div>

    <!-- Main Footer Body -->
    <div class="footer-body">
        <div class="footer-inner">

            <!-- Brand Column -->
            <div class="footer-brand">
                <a href="Home.php" class="footer-logo">
                    <i class="fas fa-heartbeat"></i> MediCare Plus
                </a>
                <p>Sri Lanka's leading digital healthcare platform connecting patients with trusted specialists. Secure appointments, medical records, and expert consultations — all in one place.</p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="#" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                    <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="Home.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Our Services</a></li>
                    <li><a href="doctors.php"><i class="fas fa-chevron-right"></i> Find a Doctor</a></li>
                    <li><a href="blog.php"><i class="fas fa-chevron-right"></i> Health Blog</a></li>
                    <li><a href="book_appointment.php"><i class="fas fa-chevron-right"></i> Book Appointment</a></li>
                    <li><a href="contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                </ul>
            </div>

            <!-- Services Column -->
            <div class="footer-col">
                <h4>Our Specialities</h4>
                <ul>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Cardiology</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Neurology</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Paediatrics</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Orthopaedics</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Gynaecology</a></li>
                    <li><a href="services.php"><i class="fas fa-chevron-right"></i> Emergency Care</a></li>
                </ul>
            </div>

            <!-- Contact Column -->
            <div class="footer-col">
                <h4>Contact Us</h4>
                <div class="footer-contact-list">
                    <div class="fc-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>45 Independence Avenue,<br>Colombo 7, Sri Lanka</span>
                    </div>
                    <div class="fc-item">
                        <i class="fas fa-phone"></i>
                        <span>+94 11 2 345 678</span>
                    </div>
                    <div class="fc-item">
                        <i class="fas fa-envelope"></i>
                        <a href="mailto:support@medicareplus.lk">support@medicareplus.lk</a>
                    </div>
                    <div class="fc-item">
                        <i class="fas fa-clock"></i>
                        <span>Mon – Sat: 8:00 AM – 8:00 PM<br>Emergency: 24/7</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Footer Bottom Bar -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <p>&copy; <?php echo date('Y'); ?> MediCare Plus (Pvt) Ltd. All rights reserved.</p>
            <div class="footer-bottom-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
                <a href="#">Cookie Policy</a>
            </div>
            <p class="footer-made">Sri Lanka's Premier Healthcare Platform 🇱🇰</p>
        </div>
    </div>

</footer>

<script>
function handleNewsletter(e) {
    e.preventDefault();
    const btn = e.target.querySelector('button');
    btn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
    btn.style.background = '#38a169';
    e.target.querySelector('input').value = '';
    setTimeout(() => { btn.innerHTML = '<i class="fas fa-paper-plane"></i> Subscribe'; btn.style.background = ''; }, 3000);
}
</script>
