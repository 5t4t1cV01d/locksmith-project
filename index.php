<?php
// ARCHIVO: index.php
$current_page = 'index';
include 'includes/header.php';
?>
<div class="video-background">
    <iframe width="1920" height="1080"
        src="https://www.youtube.com/embed/ZdTW4UICi7Y?autoplay=1&mute=1&loop=1&controls=0&start=5&end=45&playlist=ZdTW4UICi7Y"
        title="YouTube video player" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen>
    </iframe>

    <div class="hero-title-box">
        <h1>Servicio Profesional de</h1>
        <h2>CERRAJERÍA</h2>
        <a href="solicitar.php" class="option-btn hero-btn">SOLICITAR SERVICIO</a>
    </div>
</div>
<section class="about-us" id="about-us">
    <div class="about-content">
        <div class="about-text-column">
            <h2>CERRAJEROS <span>EXPERTOS</span></h2>
            <p>LA GANZUA es tu aliado de confianza en servicios de cerrajería profesional. Contamos con técnicos
                certificados disponibles las 24 horas, los 7 días de la semana, para atender desde urgencias de apertura
                de puertas hasta la instalación de sistemas de seguridad de alta gama.</p>
            <div class="about-stats">
                <div class="stat-item">
                    <img src="img/icons/clients.svg" alt="Icono de Clientes">
                    <span class="stat-number">+500</span>
                    <span class="stat-label">Clientes atendidos</span>
                </div>
                <div class="stat-item">
                    <img src="img/icons/availability.svg" alt="Icono de Disponibilidad">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Disponibilidad</span>
                </div>
                <div class="stat-item">
                    <img src="img/icons/quality.svg" alt="Icono de Garantía">
                    <span class="stat-number">+12</span>
                    <span class="stat-label">Años de experiencia</span>
                </div>
            </div>
        </div>
        <div class="about-image-column">
            <img src="img/cerrajero-1.gif" alt="Técnico cerrajero en trabajo">
        </div>
    </div>
</section>
<section class="map-section" id="location">
    <div>
        <p class="map-text">
            <strong>Ubicados estratégicamente en Mérida, Yucatán</strong>, con cobertura en toda la ciudad y municipios
            cercanos. Llegamos a tu puerta en menos de 30 minutos.
        </p>
    </div>
    <div class="map-content">
        <div class="map-embed">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7450.253560427556!2d-89.65256250642089!3d20.987554500000005!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8f5673f2bf60dec7%3A0x838fa69a42c69d12!2sLA%20GANZUA!5e0!3m2!1ses-419!2smx!4v1778345005122!5m2!1ses-419!2smx"
                width="100%" height="450" class="no-border" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="map-view-360">
            <iframe
                src="https://www.google.com/maps/embed?pb=!4v1778345816975!6m8!1m7!1s1LUufCHbyvkLx71Le9tejA!2m2!1d20.98746847783517!2d-89.6430532406101!3f358.2608454630871!4f-2.254009096408737!5f1.4602863493673333"
                width="100%" height="450" class="no-border" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>