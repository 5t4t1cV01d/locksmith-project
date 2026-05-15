<?php
// ARCHIVO: servicios.php
require_once 'conexion.php';
$current_page = 'servicios';
include 'includes/header.php';
?>

<section class="services-catalog" id="catalogo">
    <div class="catalog-header">
        <h1 class="catalog-title">SERVICIOS</h1>
        <p class="catalog-subtitle">Atendemos todo tipo de emergencias con soluciones profesionales y precios justos:
        </p>
    </div>

    <table class="catalog-table">
        <!-- Fila 1 -->
        <tr>
            <td class="catalog-item">
                <img src="img/servicios/cerrajeria-merida.jpg" alt="Cerrajería en Mérida">
                <h3>Cerrajería en Mérida</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/apertura-casas.jpg" alt="Apertura de Casas y Negocios">
                <h3>Apertura de Casas y Negocios</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/cambio-cerraduras.jpg" alt="Cambio e Instalación de Cerraduras">
                <h3>Cambio e Instalación de Cerraduras</h3>
            </td>
        </tr>

        <!-- Fila 2 -->
        <tr>
            <td class="catalog-item">
                <img src="img/servicios/duplicado-llaves.jpg" alt="Duplicado de Llaves">
                <h3>Duplicado de Llaves</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/llaves-chip.jpg" alt="Llaves con Chip">
                <h3>Llaves con Chip</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/cerraduras-digitales.jpg" alt="Cerraduras Digitales">
                <h3>Cerraduras Digitales</h3>
            </td>
        </tr>

        <!-- Fila 3 -->
        <tr>
            <td class="catalog-item">
                <img src="img/servicios/cerrajeria-automotriz.jpg" alt="Cerrajería Automotriz">
                <h3>Cerrajería Automotriz</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/cajas-fuertes.jpg" alt="Cajas Fuertes">
                <h3>Cajas Fuertes</h3>
            </td>
            <td class="catalog-item">
                <img src="img/servicios/control-acceso.jpg" alt="Control de Acceso">
                <h3>Control de Acceso</h3>
            </td>
        </tr>
    </table>
</section>

<section class="pricing-table" id="precios">
    <h2 class="pricing-title">Tarifas y precios</h2>
    <table class="pricing-main-table">
        <tr>
            <!-- Columna 1 -->
            <td class="pricing-column day-pass">
                <h3>Urgencias 24/7</h3>
                <p class="price-from">Desde</p>
                <p class="price-value large-price">$350</p>
                <p class="price-tax large-tax">MXN</p>
                <ul class="feature-list">
                    <li><span class="check-icon">✓</span> Apertura de puertas</li>
                    <li><span class="check-icon">✓</span> Atención inmediata</li>
                    <li><span class="check-icon">✓</span> Servicio 24/7 los 365 días</li>
                    <li><span class="check-icon">✓</span> Llegada en &lt;30 min</li>
                </ul>
                <a href="solicitar.php" class="option-btn">Solicitar</a>
            </td>

            <!-- Columna 2 -->
            <td class="pricing-column">
                <h3>Residencial</h3>
                <p class="price-from">Desde</p>
                <p class="price-value">$500</p>
                <p class="price-tax">MXN</p>
                <ul class="feature-list">
                    <li><span class="check-icon">✓</span> Cambio de chapas</li>
                    <li><span class="check-icon">✓</span> Instalación de cerraduras</li>
                    <li><span class="check-icon">✓</span> Duplicado de llaves</li>
                    <li><span class="check-icon">✓</span> Apertura sin daño</li>
                </ul>
                <a href="solicitar.php" class="option-btn">Solicitar</a>
            </td>

            <!-- Columna 3 -->
            <td class="pricing-column">
                <h3>Automotriz</h3>
                <p class="price-from">Desde</p>
                <p class="price-value">$600</p>
                <p class="price-tax">MXN</p>
                <ul class="feature-list">
                    <li><span class="check-icon">✓</span> Apertura de vehículos</li>
                    <li><span class="check-icon">✓</span> Programación de control remoto</li>
                    <li><span class="check-icon">✓</span> Extracción de llaves rotas</li>
                    <li><span class="check-icon">✓</span> Duplicado de llaves con chip</li>
                </ul>
                <a href="solicitar.php" class="option-btn">Solicitar</a>
            </td>

            <!-- Columna 4 -->
            <td class="pricing-column">
                <h3>Seguridad</h3>
                <p class="price-from">Desde</p>
                <p class="price-value">$1,200</p>
                <p class="price-tax">MXN</p>
                <ul class="feature-list">
                    <li><span class="check-icon">✓</span> Cerraduras de alta seguridad</li>
                    <li><span class="check-icon">✓</span> Cerraduras digitales</li>
                    <li><span class="check-icon">✓</span> Cámaras y alarmas</li>
                    <li><span class="check-icon">✓</span> Cajas fuertes</li>
                    <li><span class="check-icon">✓</span> Asesoría personalizada</li>
                </ul>
                <a href="solicitar.php" class="option-btn">Solicitar</a>
            </td>
        </tr>
    </table>
</section>

<section class="opinions-section" id="opiniones">
    <h2 class="opinions-title">LO QUE DICEN NUESTROS CLIENTES</h2>
    <p class="opinions-subtitle">La confianza de Mérida respalda nuestro trabajo profesional.</p>

    <div class="opinions-container">
        <?php
        try {
            $db = getDB();
            $query_reviews = "SELECT * FROM reviews ORDER BY created_at DESC LIMIT 6";
            $stmt_reviews = $db->query($query_reviews);
            $db_reviews = $stmt_reviews->fetchAll(PDO::FETCH_ASSOC);

            if ($db_reviews) {
                $is_admin = isset($_SESSION['user_id']); // Detectar si hay sesión iniciada
        
                foreach ($db_reviews as $rev) {
                    $stars = str_repeat('★', $rev['rating']);
                    $rev_id = $rev['id'];

                    echo '<div class="opinion-card animate-slide">';

                    // BOTÓN ELIMINAR (Solo para Administradores con Icono SVG)
                    if ($is_admin) {
                        echo '<a href="php/eliminar_opinion.php?id=' . $rev_id . '" 
                                 onclick="return confirm(\'¿Estás seguro de eliminar esta opinión?\')"
                                 class="delete-review-btn" 
                                 title="Eliminar Opinión">
                                 <img src="img/icons/cancel.svg" alt="Eliminar" class="delete-icon-img">
                              </a>';
                    }

                    echo '    <div class="stars">' . $stars . '</div>';
                    echo '    <p class="opinion-text">"' . htmlspecialchars($rev['comment']) . '"</p>';
                    echo '    <h4 class="client-name">' . htmlspecialchars($rev['client_name']) . '</h4>';
                    echo '    <span class="client-location">' . htmlspecialchars($rev['service_location']) . '</span>';
                    echo '</div>';
                }
            } else {
                // FALLBACK: Opiniones de ejemplo si la BD está vacía
                echo '
                <div class="opinion-card">
                    <div class="stars">★★★★★</div>
                    <p class="opinion-text">"Excelente servicio, llegaron en menos de 20 minutos a mi domicilio. Muy profesionales y el precio fue justo."</p>
                    <h4 class="client-name">Juan Pérez</h4>
                    <span class="client-location">Mérida Centro</span>
                </div>
                <div class="opinion-card">
                    <div class="stars">★★★★★</div>
                    <p class="opinion-text">"Cambiaron todas las cerraduras de mi nueva casa. Me dieron garantía por escrito y asesoría técnica."</p>
                    <h4 class="client-name">María García</h4>
                    <span class="client-location">Fracc. Las Américas</span>
                </div>
                <div class="opinion-card">
                    <div class="stars">★★★★★</div>
                    <p class="opinion-text">"Me quedé fuera de mi auto y lo abrieron sin dañarlo. Muy recomendados para urgencias automotrices."</p>
                    <h4 class="client-name">Ricardo Luna</h4>
                    <span class="client-location">Caucel</span>
                </div>';
            }
        } catch (Exception $e) {
            echo "<p>Error al cargar opiniones.</p>";
        }
        ?>
    </div>
</section>

<section class="spaces-section" id="proceso">
    <h2 class="spaces-title">CÓMO TRABAJAMOS</h2>
    <p class="spaces-subtitle">Proceso transparente, rápido y sin sorpresas en Mérida, Yucatán.</p>
    <div class="space-block left-aligned">
        <div class="space-info">
            <span class="space-number">01</span>
            <h3 class="space-name">SOLICITUD DE SERVICIO</h3>
            <p class="space-description">
                Contacta con nosotros vía teléfono, WhatsApp o a través de nuestro formulario en línea. Dínos tu
                ubicación y el tipo de problema que tienes.
            </p>
            <ul class="feature-list-space">
                <li><span class="check-icon">✓</span> Formulario online disponible 24/7</li>
                <li><span class="check-icon">✓</span> Atención vía WhatsApp inmediata</li>
                <li><span class="check-icon">✓</span> Diagnóstico inicial sin costo</li>
            </ul>
        </div>
        <div class="space-image">
            <img src="img/01 - Solicitud de Servicio.png" alt="Solicitud de servicio de cerrajería">
        </div>
    </div>
    <div class="space-block right-aligned">
        <div class="space-image">
            <img src="img/02 - Tecnico en Camino.png" alt="Técnico en camino">
        </div>
        <div class="space-info">
            <span class="space-number">02</span>
            <h3 class="space-name">TÉCNICO EN CAMINO</h3>
            <p class="space-description">
                Asignamos al técnico más cercano a tu ubicación. Llegará en menos de 30 minutos con todo el equipo
                necesario.
            </p>
            <ul class="feature-list-space">
                <li><span class="check-icon">✓</span> Rastreo en tiempo real</li>
                <li><span class="check-icon">✓</span> Técnicos certificados</li>
                <li><span class="check-icon">✓</span> Uniforme e identificación oficial</li>
            </ul>
        </div>
    </div>
    <div class="space-block left-aligned">
        <div class="space-info">
            <span class="space-number">03</span>
            <h3 class="space-name">DIAGNÓSTICO Y COTIZACIÓN</h3>
            <p class="space-description">
                El técnico evaluará el problema en sitio y te dará una cotización clara antes de iniciar cualquier
                trabajo. Sin costos ocultos.
            </p>
            <ul class="feature-list-space">
                <li><span class="check-icon">✓</span> Precio fijo antes de iniciar</li>
                <li><span class="check-icon">✓</span> Sin cobros adicionales sorpresa</li>
                <li><span class="check-icon">✓</span> Múltiples opciones de pago</li>
            </ul>
        </div>
        <div class="space-image">
            <img src="img/03 - Diagnostico y Cotizacion.png" alt="Diagnóstico de cerrajería">
        </div>
    </div>
    <div class="space-block right-aligned">
        <div class="space-image">
            <img src="img/04 - Servicio y Garantia.png" alt="Servicio completado">
        </div>
        <div class="space-info">
            <span class="space-number">04</span>
            <h3 class="space-name">SERVICIO Y GARANTÍA</h3>
            <p class="space-description">
                Realizamos el trabajo con herramientas especializadas. Al finalizar, te entregamos factura y garantía
                por escrito en todos nuestros servicios.
            </p>
            <ul class="feature-list-space">
                <li><span class="check-icon">✓</span> Garantía de 6 meses en mano de obra</li>
                <li><span class="check-icon">✓</span> Facturación electrónica disponible</li>
                <li><span class="check-icon">✓</span> Servicio post-venta incluido</li>
            </ul>
        </div>
    </div>
</section>
<?php include 'includes/footer.php'; ?>