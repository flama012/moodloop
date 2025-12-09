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

## 📊 Estadísticas del repositorio
- **Lenguaje principal:** ![PHP](https://img.shields.io/badge/PHP-8.2-blue?logo=php)  
- **Lenguajes usados:**  
  ![HTML](https://img.shields.io/badge/HTML-20%25-orange?logo=html5)  
  ![CSS](https://img.shields.io/badge/CSS-30%25-blue?logo=css3)  
  ![JavaScript](https://img.shields.io/badge/JavaScript-20%25-yellow?logo=javascript)  
  ![PHP](https://img.shields.io/badge/PHP-30%25-purple?logo=php)  

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
- Ajusta las variables de correo (SMTP, usuario, contraseña) en `backend/Correo.php`.  
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

---

## ✅ Funcionalidades principales

- Registro con verificación por correo  
- Inicio de sesión  
- Feed filtrable por:
  - Seguidos  
  - Emoción del día  
  - Emoción específica  
  - Etiquetas  
  - Todas las publicaciones  
- Sistema de seguidores  
- Likes y comentarios  
- Perfil editable  
- Buscador de usuarios  

---

## ✅ Backend (visión general)

### UsuarioBBDD
Gestión de usuarios:
- Registro  
- Login  
- Seguidores y seguidos  
- Biografía  
- Estado emocional  

### PublicacionBBDD
Gestión de publicaciones:
- Crear publicaciones  
- Asignar etiquetas  
- Gestionar likes  
- Gestionar comentarios  
- Obtener publicaciones con distintos filtros  

### Correo
- Envío de email de verificación de cuenta  

---

## ✅ Frontend (páginas principales)

### login.php
Formulario de inicio de sesión.

### registro.php
Formulario de registro y envío de correo de verificación.

### pagina_feed.php
Feed principal con filtros por emoción, etiquetas, seguidos, etc.

### pagina_usuario.php
Perfil del usuario logueado (biografía, estado emocional, publicaciones propias).

### pagina_publicacion.php
Formulario para crear nuevas publicaciones.

### ver_perfil.php
Visualizar el perfil de otros usuarios y seguir/dejar de seguir.

---

## ✅ Flujo de verificación

1. El usuario se registra.  
2. Se genera un token único.  
3. Se envía un email con el enlace de verificación.  
4. El usuario accede a `verificar.php`.  
5. La cuenta queda activada.  

---

## ✅ Manual de uso

### Registro
1. Accede a la página de registro.  
2. Introduce nombre, correo y contraseña.  
3. Acepta los términos.  
4. Revisa tu correo y confirma tu cuenta.  

### Inicio de sesión
1. Introduce tu correo y contraseña.  
2. Si tu correo no está verificado, el sistema te avisará.  

### Feed
Puedes ver publicaciones de:
- Personas que sigues  
- Tu emoción del día  
- Una emoción específica  
- Determinadas etiquetas  
- Todas las publicaciones  

### Crear publicación
1. Escribe un mensaje.  
2. Selecciona tu emoción.  
3. Añade etiquetas opcionales.  

### Perfil
- Edita tu biografía.  
- Cambia tu estado emocional.  
- Consulta tus publicaciones.  

### Seguir usuarios
- Entra en su perfil.  
- Pulsa “Seguir”.  

---

## ✅ Documentación técnica (resumen)

### Arquitectura
- PHP + MySQL  
- Backend orientado a objetos  
- Frontend basado en plantillas PHP  
- Sesiones para autenticación  

### Clases principales
- `UsuarioBBDD`: usuarios, seguidores, perfil  
- `PublicacionBBDD`: publicaciones, likes, comentarios  
- `Correo`: verificación por email  

### Flujo de autenticación
1. Registro → token → correo  
2. Verificación → activar cuenta  
3. Login → sesiones  

### Flujo de publicaciones
1. Crear publicación  
2. Insertar etiquetas  
3. Mostrar en feed  
4. Likes y comentarios  
