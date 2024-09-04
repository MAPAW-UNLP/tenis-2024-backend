# Club de tenis

## Descripción

Este repositorio contiene la API Rest del proyecto utilizado en la cursada 2024 de la materia [Métodos Ágiles para Aplicaciones Web (MAPAW)](https://www.info.unlp.edu.ar/wp-content/uploads/2023/03/Metodos-Agiles-para-Aplicaciones-Web_2023.pdf) de la Facultad de Informática, Universidad Nacional de La Plata.

## Instalación
El proyecto está desarrollado con PHP 7.3 utilizando [Symfony](https://symfony.com/) para la API y MySQL como base de datos. 

Para la instalación se recomienda utilizar Docker que facilita la instalación de las dependencias.

1. Clonar el repositorio.
2. Instalar [Docker](https://www.docker.com/).
3. Ejecutar `docker-compose up -d` para construir las imágenes e iniciar los contenedores.
4. Ejecutar `docker exec -it tenis_php composer install` para instalar las dependencias.
5. Ejecutar `docker exec -it tenis_php php bin/console doctrine:migrations:migrate` para correr las migraciones de la base de datos.
6. Ingresando a la URL `http://localhost:8083/` se debe ver una página de bienvenida de Symfony.


## Utilidades
* Iniciar contenedores: `docker-compose start`.
* Detener contenedores: `docker-compose stop`.
* Eliminar contenedores: `docker-compose down`.
* Para ingresar a la base de datos, se puede utilizar cualquier cliente de MySQL como [MySQL Workbench](https://dev.mysql.com/downloads/workbench/). Las credenciales son:
    * host: localhost.
    * base de datos: tenis.
    * usuario: tenis.
    * password: tenis.
    * puerto: 3309. 

