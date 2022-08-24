# Geolocation Attendance Control

[![License](https://img.shields.io/badge/License-MIT-9b59b6.svg)](LICENSE)

**Traducciones**: [English](/README.md)

Plugin de WordPress para control de asistencia de monitores en actividades extraescolares.

---

- [Requisitos](#requisitos)
- [Instalación](#instalación)
- [Características](#características)
- [Imágenes](#imágenes)
- [Patrocinar](#patrocinar)
- [Licencia](#licencia)

---

**Si haces uso de este proyecto**, ten en cuenta que la geolocalización es fácilmente manipulable
por el usuario. En su momento no se desarrolló ninguna característica para intentar evitarlo ya que
no era necesario para el propósito del proyecto.

## Requisitos

Fue desarrollado bajo WordPress 5 y PHP 7.

## Instalación

A efectos de prueba, puedes clonar todo el repositorio en el directorio de plugins de WordPress:

```console
git clone https://github.com/josantonius/wp-geolocation-attendance-control.git
```

- Añadir la `google_api_key` en el archivo `config/setting.php`.

- Crear una página para el control de asistencia e incluir el shortcode `geolocation-attendance-control`.

## Características

- Gestión de centros educativos.
- Gestión de monitores educativos.
- Gestión de actividades extraescolares.
- Control de asistencia por geolocalización.
- Generación de informes por fecha.
- Gestión de registros de asistencia.
- Control de entradas y salidas.
- Control de horario y ubicación.

## Imágenes

### Gestión de centros educativos

![image](/resources/back-education-centers.png)
![image](/resources/back-edit-education-center-1.png)
![image](/resources/back-edit-education-center-2.png)
![image](/resources/back-education-center-details.png)

### Gestión de actividades extracurriculares

![image](/resources/back-activity-list.png)
![image](/resources/back-edit-activity.png)

### Registros de asistencia

![image](/resources/back-attendance-list.png)
![image](/resources/back-remove-attendance.png)
![image](/resources/back-attendance-calendar.png)
![image](/resources/back-attendance-selection.png)
![image](/resources/back-attendance-csv.png)

### Control de asistencia

![image](/resources/front-checking.png)

### Avisos posteriores a la comprobación

![image](/resources/front-fail-hour.png)
![image](/resources/front-success-checking.png)
![image](/resources/front-fail-location.png)

## Patrocinar

Si este proyecto te ayuda a reducir el tiempo de desarrollo,
[puedes patrocinarme](https://github.com/josantonius/lang/es-ES/README.md#patrocinar)
para apoyar mi trabajo :blush:

## Licencia

Este repositorio tiene una licencia [MIT License](LICENSE).

Copyright © 2018-2022, [Josantonius](https://github.com/josantonius/lang/es-ES/README.md#contacto)
