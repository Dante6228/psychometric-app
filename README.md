# 🧠 Plataforma Web para Test Psicométrico DUAL

**Test Psicométrico DUAL** es una plataforma web diseñada para **automatizar** y **digitalizar** el proceso de evaluación de aspirantes al programa **DUAL**, facilitando tanto la experiencia del alumno como el análisis administrativo por parte del personal escolar.

---

## 🎯 Objetivos del Proyecto

- 📋 Recolectar datos psicométricos de aspirantes mediante un test en línea.
- 🧪 Comparar resultados individuales con un **perfil ideal** previamente definido.
- 📊 Visualizar, analizar y exportar resultados de forma sencilla mediante gráficas dinámicas.

---

## 🧰 Tecnologías Utilizadas

| Área             | Tecnologías                              |
|------------------|-------------------------------------------|
| **Frontend**     | HTML5, CSS3, JavaScript, Tailwind CSS     |
| **Backend**      | PHP + MySQL                               |
| **Contenedores** | Docker                                    |
| **Autenticación**| PHP con manejo de sesiones                |
| **Gestión**      | Trello, Notion                            |

---

## 📦 Requisitos Previos

- Tener instalado [Docker](https://www.docker.com/)
- Tener instalado [Node.js y NPM](https://nodejs.org/)
- Tener instalado [Composer](https://getcomposer.org/)
- Conexión a internet para descargar contenedores y dependencias

---

## ⚙️ Cómo Ejecutar el Proyecto

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/Dante6228/psychometric-app.git
cd psychometric-app
```

### 2️⃣ Instalar dependencias PHP con Composer

```bash
composer install
```

> Esto instalará las dependencias necesarias definidas en `composer.json`. Asegúrate de tener Composer instalado y configurado en tu sistema.

### 3️⃣ Instalar dependencias de Node (Tailwind, etc.)

```bash
npm install
```

> Esto instalará Tailwind CSS y otras dependencias listadas en `package.json`.

### 4️⃣ Compilar Tailwind CSS

```bash
npm run dev
```

> Este comando ejecuta Vite o el build configurado para Tailwind, generando los estilos necesarios. También puedes usar `npm run build` para producción.

### 5️⃣ Construir y levantar los contenedores

```bash
docker-compose up --build
```

> Esto creará los servicios definidos en `docker-compose.yml` y dejará la aplicación lista para usarse.

### 6️⃣ Acceder desde el navegador

```bash
http://localhost:8080
```

> ⚠️ Asegúrate de que el servidor web y la base de datos estén activos.

### 7️⃣ Detener los contenedores

```bash
docker-compose down
```

---

## 🗄️ Acceso a la Base de Datos

Puedes conectarte con cualquier cliente MySQL utilizando:

- **Host:** `mysql-psico`  
- **Puerto:** `3306`  
- **Usuario:** `root`  
- **Contraseña:** `root`

---

## 🌐 Acceso a la Base de Datos

Si prefieres usar una interfaz web para gestionar la base de datos, puedes acceder a phpMyAdmin:

- **URL:** `http://localhost:8081` 
- **Usuario:** `root`  
- **Contraseña:** `root`

---

## 👥 Equipo de Desarrollo

Este proyecto fue desarrollado por estudiantes del grupo **DUAL 601 - CECYTEM Cuautitlán**, en colaboración:

### 💡 Integrantes y Roles

- **🎹 Dante Alejandro Viveros Rodríguez**  
  *Representante del equipo y desarrollador Full Stack*  
  Desarrollador Full Stack, líder técnico y representante del equipo,
  encargado de coordinar y guiar al equipo durante el desarrollo, integrar el frontend y backend, estructurar la base del sistema y gestionar la infraestructura con Docker.

- **💻 Alan Nayet Briones Galván**  
  *Desarrollador Backend*  
  Responsable del diseño de base de datos, la lógica del servidor y el manejo de conexiones usando PHP y MySQL.  

- **🎨 Valeria Sofía Vivas Vargas**  
  *Diseñadora UI/UX y desarrolladora frontend*  
  Encargada del diseño visual y la experiencia de usuario, creando prototipos en Figma e implementando estilos con Tailwind CSS.  

- **🖌️ Areli Janeth Embriz Rodríguez**  
  *Diseñadora UI/UX y desarrolladora frontend*  
  Apoya en el diseño de interfaces intuitivas, maqueta prototipos y colabora en la implementación del diseño responsivo.

---

## 📄 Licencia

Este proyecto está licenciado bajo la **Creative Commons Atribución-NoComercial 4.0 Internacional (CC BY-NC 4.0)**.

> Esto significa que puedes **usar, modificar y compartir** el proyecto, **siempre y cuando no lo uses con fines comerciales** y **des el crédito correspondiente** a los autores.

🔗 [https://creativecommons.org/licenses/by-nc/4.0/deed.es](https://creativecommons.org/licenses/by-nc/4.0/deed.es)

---
