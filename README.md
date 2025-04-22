# View Media Creative Solutions

Sitio web oficial de View Media Creative Solutions, una agencia multimedia especializada en producción audiovisual, video profesional, registros aéreos y fotografía profesional.

## Características

- Diseño responsivo
- Galería de imágenes interactiva
- Formulario de contacto
- Animaciones personalizadas
- Sección de servicios
- Galería de producciones

## Tecnologías utilizadas

- HTML5
- CSS3
- JavaScript
- PHP (para el formulario de contacto)
- Swiper.js (para el carrusel de producciones)

## Estructura del proyecto

```
viewmedia/
├── assets/
│   ├── css/
│   │   └── main.css
│   └── js/
│       └── main.js
├── images/
│   ├── producciones/
│   └── Servicios/
├── index.html
├── send_email.php
└── README.md
```

## Configuración

1. Clona el repositorio:
```bash
git clone https://github.com/tu-usuario/viewmedia.git
```

2. Configura el formulario de contacto:
- Edita el archivo `send_email.php` con tu correo de contacto
- Asegúrate de que el servidor tenga PHP configurado

## Despliegue en GitHub Pages

1. Crea un nuevo repositorio en GitHub
2. Sube los archivos:
```bash
git init
git add .
git commit -m "Initial commit"
git remote add origin https://github.com/tu-usuario/viewmedia.git
git push -u origin main
```

3. En la configuración del repositorio:
   - Ve a "Settings" > "Pages"
   - Selecciona la rama "main" como fuente
   - Elige la carpeta "/ (root)" como directorio de publicación

## Notas importantes

- El formulario de contacto requiere un servidor con PHP
- Las imágenes deben estar optimizadas para web
- Se recomienda usar un CDN para los recursos externos

## Licencia

Este proyecto está bajo la licencia MIT. 