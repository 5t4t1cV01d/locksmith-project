# LA GANZUA - Sistema de Gestión de Cerrajería Profesional 🔑

![Logo](img/LA%20GANZUA.png)

**LA GANZUA** es una plataforma web de alto rendimiento diseñada para modernizar y optimizar los servicios de cerrajería. El sistema se enfoca en una experiencia de usuario (UX) sin fricciones, permitiendo solicitudes de servicio ultra-rápidas y una gestión administrativa centralizada.

## 🚀 Características Principales

### 📋 Solicitudes Express (Frontend)
- **Identificación por Teléfono:** Sistema inteligente que reconoce a clientes existentes para un flujo de solicitud en menos de 10 segundos.
- **Validación Segmentada:** Errores visuales sin recarga de página (AJAX-style) para una corrección inmediata.
- **Blindaje de Datos:** Filtro en tiempo real que solo permite caracteres numéricos y limita a 10 dígitos.
- **Privacidad y Control:** Los clientes no registrados son redirigidos a contacto directo, asegurando un crecimiento controlado de la base de datos.

### 📅 Agenda Inteligente
- **Selector de Citas Dinámico:** Solo visible para servicios programados (No Urgencias).
- **Horarios Laborales Estrictos:** Configurado según la disponibilidad del negocio (L-S, 9:00-13:00 y 16:00-21:00).
- **Validación de Calendario:** Bloqueo de fechas pasadas y domingos.

### 🛠️ Panel Administrativo (Backend)
- **Dashboard Premium:** Diseño industrial-moderno optimizado para la gestión rápida de órdenes.
- **Creación de Órdenes:** Flujo de registro completo (Nombre, Edad, Email, Dirección) sincronizado con el buscador de clientes.
- **Gestión de Estados:** Seguimiento en tiempo real de cada solicitud de servicio.

## 🛠️ Tecnologías Utilizadas

- **Frontend:** HTML5, CSS3 (Vanilla con arquitectura de variables), JavaScript (Vanilla ES6).
- **Backend:** PHP 7.4+ (Arquitectura modular).
- **Base de Datos:** MySQL / MariaDB (Optimizado para relaciones cliente-servicio).
- **Diseño:** Estética industrial premium basada en una paleta de Grises Metálicos y Ámbar de seguridad.

## 💻 Instalación Local

1. Clonar el repositorio:
   ```bash
   git clone https://github.com/5t4t1cV01d/locksmith-project.git
   ```
2. Mover la carpeta a su servidor local (ej. `htdocs` en XAMPP).
3. Importar la base de datos (Archivo SQL incluido).
4. Configurar las credenciales en `conexion.php`.
5. Acceder vía navegador a `http://localhost/locksmith-project`.

---
*Proyecto desarrollado y optimizado para cumplir con los más altos estándares de UX y requerimientos técnicos modernos.*
