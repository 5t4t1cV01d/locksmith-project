<?php
// ARCHIVO: contacto.php
$current_page = 'contacto'; 
include 'includes/header.php'; 
?>
<section class="contact-section" id="contacto">
    <h2 class="contact-title">Contáctanos</h2>

    <div class="contact-content-wrapper">
        <?php if (isset($_GET['status'])): ?>
            <div class="status-alert <?php echo ($_GET['status'] == 'success_opinion') ? 'success' : 'error'; ?> animate-slide contact-alert">
                <?php 
                    if ($_GET['status'] == 'success_opinion') {
                        echo "<b>✅ ¡GRACIAS POR TU OPINIÓN!</b><br>Tu reseña ha sido recibida y será publicada tras ser revisada por un administrador.";
                    } else {
                        echo "<b>❌ ERROR:</b> " . htmlspecialchars($_GET['msg'] ?? 'No se pudo procesar tu opinión.');
                    }
                ?>
            </div>
        <?php endif; ?>

        <div class="contact-form-wrapper" id="form-content">
            <p class="form-instruction">¿Cómo fue tu experiencia con <strong>LA GANZUA</strong>? Tu opinión nos ayuda a seguir brindando un servicio de excelencia en Mérida.</p>

            <form id="opinion-form" action="php/guardar_opinion.php" method="POST">
                <!-- Calificación por Estrellas -->
                <div class="form-group">
                    <label>CALIFICACIÓN DEL SERVICIO:</label>
                    <div class="star-rating">
                        <input type="radio" id="star5" name="rating" value="5" required /><label for="star5" title="Excelente">★</label>
                        <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Muy Bueno">★</label>
                        <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Bueno">★</label>
                        <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Regular">★</label>
                        <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Malo">★</label>
                    </div>
                </div>

                <!-- Comentario -->
                <div class="form-group">
                    <label for="comentario">TU EXPERIENCIA:</label>
                    <textarea id="comentario" name="comentario" rows="4" required
                        placeholder="Cuéntanos qué tal te atendió nuestro técnico..."></textarea>
                </div>

                <div class="form-group-inline" data-layout="half-half">
                    <!-- Nombre -->
                    <div class="form-group half-width">
                        <label for="nombre">TU NOMBRE:</label>
                        <input type="text" id="nombre_opinion" name="nombre" placeholder="Ej. Juan P.">
                        <div class="anonymous-toggle">
                            <input type="checkbox" id="anonimo" name="anonimo">
                            <label for="anonimo">Publicar como anónimo</label>
                        </div>
                    </div>

                    <!-- Lugar -->
                    <div class="form-group half-width">
                        <label for="ubicacion">ZONA DEL SERVICIO:</label>
                        <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej. Mérida Centro / Caucel" required>
                    </div>
                </div>

                <button type="submit" id="submit-btn" class="option-btn">Publicar Opinión</button>
            </form>
        </div>

        <div class="info-reveal-box" id="info-after-submit">
            <div id="success-message" class="u-hidden">
            </div>

            <div class="info-box">
                <div class="info-columns-wrapper"> 
                    
                    <div class="info-column left-half">
                        <h3 class="contact-details-title">Canales Directos</h3>
                        <div class="contact-items-wrapper">
                            <div class="info-item">
                                <img src="img/icons/call.svg" alt="Teléfono">
                                <p>Teléfono: <strong>(999) 171 7545</strong></p>
                            </div>
                            <div class="info-item">
                                <img src="img\icons\whats.svg" alt="WhatsApp">
                                <p>WhatsApp (Urgencias): <strong>999 508 1252</strong></p>
                            </div>
                            <div class="info-item">
                                <img src="img/icons/mail.svg" alt="Email">
                                <p>Email: <strong>contacto@cerramax.mx</strong></p>
                            </div>
                        </div>
                    </div>

                    <div class="info-column right-half">
                        <h3 class="social-title">Síguenos en Redes</h3>
                        <div class="social-links">
                            <a href="https://www.facebook.com   " target="_blank" title="Facebook"><img src="img/icons/facebook.svg"
                                    alt="Facebook"></a>
                            <a href="https://www.instagram.com" target="_blank" title="Instagram"><img src="img/icons/instagram.svg"
                                    alt="Instagram"></a>
                            <a href="https://www.linkedin.com/in/gabriel-ernesto-tamayo-154016398/" target="_blank" title="LinkedIn"><img src="img/icons/linkedin.svg"
                                    alt="LinkedIn"></a>
                        </div>
                    </div>
                </div>
                </div>
        </div>
    </div>
</section>
<script src="js/contacto.js"></script> 
<?php include 'includes/footer.php'; ?>