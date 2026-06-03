    </main>

    <footer class="site-footer">
        <div class="footer-grid">
            <div class="footer-block">
                <h3>About MediCare Plus Sri Lanka</h3>
                <p>Sri Lanka's leading digital healthcare platform connecting patients with trusted specialists across the island. Secure appointments, medical records, and expert consultation all in one place.</p>
            </div>
            <div class="footer-block">
                <h3>Quick Links</h3>
                <ul>
                    <li><a href="Home.php">Home</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="doctors.php">Find a Doctor</a></li>
                    <li><a href="blog.php">Health Blog</a></li>
                </ul>
            </div>
            <div class="footer-block">
                <h3>Contact Information</h3>
                <address>
                    MediCare Plus Healthcare Ltd.<br>
                    45 Independence Avenue,<br>
                    Colombo 7, Sri Lanka 00700<br>
                    Phone: +94 11 2 345 678<br>
                    Email: <a href="mailto:support@medicareplus.lk">support@medicareplus.lk</a>
                </address>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> MediCare Plus (Pvt) Ltd. All rights reserved. | Sri Lanka's Premier Healthcare Platform</p>
            <p>Providing safe, responsive, and accessible patient care management across the nation.</p>
        </div>
    </footer>

    <script>
        // Minimal enhancement for navigation accessibility and responsive fallback.
        document.addEventListener('DOMContentLoaded', function() {
            var nav = document.querySelector('.primary-nav');
            if (!nav) return;
            var links = nav.querySelectorAll('a');
            links.forEach(function(link) {
                link.addEventListener('focus', function() {
                    link.classList.add('focus-ring');
                });
                link.addEventListener('blur', function() {
                    link.classList.remove('focus-ring');
                });
            });
        });
    </script>
    </body>

    </html>