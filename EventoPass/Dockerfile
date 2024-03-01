# Utiliza la imagen oficial de PHP con CLI
FROM php:7.4-cli

# Establece el directorio de trabajo en /app
WORKDIR /app

# Copia el contenido de tu proyecto al contenedor
COPY . .

# CMD define el comando predeterminado a ejecutar cuando se inicia el contenedor
CMD ["php", "artisan", "migrate"]