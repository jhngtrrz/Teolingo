# Guía de instalación de BibleOL con Docker

Esta guía explica cómo configurar BibleOL utilizando Docker de forma sencilla sin necesidad de configuraciones manuales complejas.

## Requisitos previos

- Docker instalado
- Docker Compose instalado

## Instalación

1. Crea un archivo llamado `docker-compose-ultra-simple.yml` con el siguiente contenido:

```yaml
version: '3.8'

services:
  app:
    image: tmccormack14/bibleol-amd:2023_12_12
    container_name: bibleol-app
    ports:
      - "8000:80"
    environment:
      - MYSQL_USER=tim
      - MYSQL_PASSWORD=pwd
      - MYSQL_DATABASE=bibleol
      - BASE_URL=http://localhost:8000
    command: >
      /bin/bash -c "
      service mysql start &&
      sleep 5 &&
      mysql -u root -e \"CREATE USER IF NOT EXISTS 'tim'@'localhost' IDENTIFIED BY 'pwd'; CREATE DATABASE IF NOT EXISTS bibleol; GRANT ALL PRIVILEGES ON *.* TO 'tim'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;\" &&
      cd /var/www/html/BibleOL/myapp/config &&
      cp config.php-dist config.php &&
      cp database.php-dist database.php &&
      cp ol.php-dist ol.php &&
      sed -i -e \"s@https://example.com@http://localhost:8000@g\" config.php &&
      sed -i -e \"s@USERNAME@tim@g\" database.php &&
      sed -i -e \"s@PASSWORD@pwd@g\" database.php &&
      sed -i -e \"s@DATABASE@bibleol@g\" database.php &&
      sed -i -e \"s@localhost@127.0.0.1@g\" database.php &&
      sed -i -e \"s@array()@array('MyBH', 'RRG', 'Hinneh', 'AndrewsUniversity')@g\" ol.php &&
      cd /var/www/html/BibleOL &&
      mysql -u root bibleol < bolsetup.sql &&
      ./setup_lang.sh &&
      echo 'ServerName localhost' >> /etc/apache2/apache2.conf &&
      a2enmod rewrite &&
      echo \"<Directory /var/www/html/BibleOL>\" >> /etc/apache2/apache2.conf &&
      echo \"  AllowOverride All\" >> /etc/apache2/apache2.conf &&
      echo \"</Directory>\" >> /etc/apache2/apache2.conf &&
      chown -R www-data:www-data /var/www/html/BibleOL &&
      chmod -R 775 /var/www/html/BibleOL &&
      service apache2 restart &&
      sleep 2 &&
      php index.php users generate_administrator admin 'Default Admin' bibleol_pwd &&
      tail -f /dev/null
      "
```

2. Inicia el contenedor usando el siguiente comando:

```bash
docker-compose -f docker-compose-ultra-simple.yml up -d
```

3. Espera aproximadamente 2-3 minutos para que se complete la configuración.

## Acceso a la aplicación

Una vez que el contenedor esté en funcionamiento, puedes acceder a BibleOL en:

- URL: http://localhost:8000
- Usuario: admin
- Contraseña: bibleol_pwd

## Comandos útiles

- **Ver logs del contenedor**:
  ```bash
  docker logs -f bibleol-app
  ```

- **Detener el contenedor**:
  ```bash
  docker-compose -f docker-compose-ultra-simple.yml down
  ```

- **Reiniciar el contenedor**:
  ```bash
  docker-compose -f docker-compose-ultra-simple.yml restart
  ```

## Solución de problemas

### Problemas con permisos de Apache
Si encuentras errores de "Forbidden" o problemas de acceso, verifica que la configuración de Apache esté correcta en el contenedor:

```bash
docker exec -it bibleol-app bash -c "cat /etc/apache2/apache2.conf | grep -A 5 'Directory /var/www/html/BibleOL'"
```

### Problemas con la base de datos
Si la aplicación muestra errores de conexión a la base de datos, verifica que MySQL esté ejecutándose:

```bash
docker exec -it bibleol-app bash -c "service mysql status"
```

Y verifica las credenciales:

```bash
docker exec -it bibleol-app bash -c "cat /var/www/html/BibleOL/myapp/config/database.php | grep -A 5 'hostname'"
```

## Explicación del proceso

El script de configuración realiza las siguientes acciones:

1. Inicia el servidor MySQL
2. Crea un usuario y base de datos para la aplicación
3. Configura los archivos PHP necesarios
4. Importa la base de datos inicial
5. Configura los idiomas y lexicones
6. Configura Apache para permitir .htaccess
7. Establece los permisos correctos de archivos
8. Crea un usuario administrador
9. Inicia el servidor web

Este enfoque automatizado evita la necesidad de configuración manual y asegura que todos los componentes estén correctamente configurados. 