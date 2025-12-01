# MoodLoop — Red Social Emocional

![Estado del proyecto](https://img.shields.io/badge/status-en%20desarrollo-yellow)
![Lenguajes](https://img.shields.io/github/languages/top/flama012/moodloop)
![Commits](https://img.shields.io/github/commit-activity/m/flama012/moodloop)
![Último commit](https://img.shields.io/github/last-commit/flama012/moodloop)

---

## 🌟 Descripción
MoodLoop es una red social que permite a los usuarios compartir publicaciones vinculadas a su estado emocional diario.  
El sistema adapta el feed según emociones y etiquetas frecuentes, fomentando la empatía y la expresión emocional.

---

## 🛠️ Tecnologías principales

| Categoría       | Tecnologías usadas |
|-----------------|--------------------|
| **Frontend**    | HTML, CSS, JavaScript |
| **Backend**     | PHP (PDO, sesiones) |
| **Base de datos** | MySQL (phpMyAdmin) |
| **Servidor local** | XAMPP |
| **Dependencias** | Composer, PHPMailer |

---

## 📊 Estadísticas del repositorio
- **Lenguaje principal:** ![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)  
- **Lenguajes usados:**  
  ![HTML](https://img.shields.io/badge/HTML-30%25-orange?logo=html5)  
  ![CSS](https://img.shields.io/badge/CSS-25%25-blue?logo=css3)  
  ![JavaScript](https://img.shields.io/badge/JavaScript-20%25-yellow?logo=javascript)  
  ![PHP](https://img.shields.io/badge/PHP-25%25-purple?logo=php)  

*(Los porcentajes son ilustrativos, puedes ajustarlos con datos reales de GitHub)*

---
## 🛠️ Tecnologías
- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Base de datos**: MySQL (phpMyAdmin)
- **Servidor local**: XAMPP

## 📁 Estructura del proyecto
- `/frontend`: Interfaz del usuario
- `/backend`: Lógica del servidor en PHP
- `/database`: Scripts SQL y datos iniciales
- `/assets`: Imágenes, íconos y recursos visuales
- `/docs`: Documentación técnica y visual

## 📌 Requisitos previos
- PHP >= 8.2  
- Composer instalado en tu sistema  
- XAMPP actualizado (Apache + MySQL)  
- Navegador moderno (Chrome, Firefox, Edge)  

## ⚙️ Configuración inicial
- Configura las credenciales de la base de datos en `backend/ConexionDB.php`.  
- Ajusta las variables de correo (SMTP, usuario, contraseña) en `backend/send.php`.  
- Verifica permisos de escritura en carpetas como `/uploads` si se usan.

## 🚀 Instalación

1. Clonar el repositorio dentro de la carpeta `htdocs` de XAMPP.  
   La carpeta debe llamarse exactamente `moodloop`:
```markdown
   cd C:\xampp\htdocs
```
```markdown
   git clone https://github.com/flama012/moodloop.git
```

2. Importar el archivo `moodloop.sql` en phpMyAdmin para crear la base de datos.

3. Asegúrate de tener instalado el directorio `vendor` dentro de la carpeta principal de XAMPP:

   C:\xampp\vendor

   Este directorio contiene las dependencias necesarias para funciones como el envío de correos.  
   Si no lo tienes, puedes instalarlo desde la raíz del proyecto ejecutando:

```markdown
   composer install
```

   Esto descargará las librerías necesarias (como PHPMailer) y generará la carpeta `vendor`.

4. Ejecutar XAMPP y activar los módulos Apache y MySQL.

5. Acceder al proyecto desde el navegador:

```markdown
   http://localhost/moodloop/index.php
```

## ❗ Aviso importante  
Antes de clonar el repositorio, asegúrate de hacerlo dentro de una carpeta vacía.  
Si lo haces en un proyecto abierto, todos los archivos y recursos de MoodLoop se mezclarán con los existentes, lo que puede provocar errores o conflictos en la estructura.

