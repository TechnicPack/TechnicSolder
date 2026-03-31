# Web Server Configuration

This guide provides nginx and Apache virtual host configurations for both the Solder application and the mod repository.

!!! info "Docker users can skip this"
    If you are running Solder with Docker, the bundled web server is already configured. These examples are only needed for bare-metal installations.

## Solder Application

The application server block points its document root at Solder's `public/` directory and routes all requests through `index.php`.

=== "nginx"

    ```nginx
    server {
        listen 80;
        listen [::]:80;
        server_name solder.example.com;

        access_log /var/log/nginx/solder.example.com_access.log;
        error_log  /var/log/nginx/solder.example.com_error.log;

        root  /var/www/solder/TechnicSolder/public;
        index index.php;

        location / {
            try_files $uri $uri/ /index.php?$query_string;
        }

        location ~* \.php$ {
            fastcgi_pass            unix:/run/php/php8.4-fpm.sock;
            fastcgi_index           index.php;
            fastcgi_split_path_info ^(.+\.php)(.*)$;
            include                 fastcgi_params;
            fastcgi_param PATH_INFO       $fastcgi_path_info;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

            # If behind a load balancer that terminates TLS:
            # fastcgi_param HTTPS on;
        }

        # Deny access to hidden files (except .well-known for ACME challenges)
        location ~ /\.(?!well-known).* {
            deny all;
            access_log off;
            log_not_found off;
            return 404;
        }

        # Cache static assets
        location ~* \.(?:ico|css|js|jpe?g|JPG|png|svg|woff)$ {
            expires 365d;
        }
    }
    ```

=== "Apache"

    ```apache
    <VirtualHost *:80>
        ServerName solder.example.com
        ServerAdmin admin@example.com
        DocumentRoot /var/www/html/TechnicSolder/public/

        <Directory /var/www/html/TechnicSolder/public>
            Options FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/solder_error.log
        CustomLog ${APACHE_LOG_DIR}/solder_access.log combined
    </VirtualHost>
    ```

## Mod Repository

The repository server block serves mod zip files as static downloads. The nginx config enables directory listing so you can browse available files; Apache achieves the same with `+Indexes`.

=== "nginx"

    ```nginx
    server {
        listen 80;
        listen [::]:80;
        server_name repo.example.com;

        access_log /var/log/nginx/repo.example.com_access.log;
        error_log  /var/log/nginx/repo.example.com_error.log;

        root /var/www/repo;

        location / {
            try_files $uri $uri/ =404;
            autoindex on;
            autoindex_exact_size off;
            autoindex_localtime on;
        }

        # Deny access to hidden files (except .well-known for ACME challenges)
        location ~ /\.(?!well-known).* {
            deny all;
            access_log off;
            log_not_found off;
            return 404;
        }
    }
    ```

=== "Apache"

    ```apache
    <VirtualHost *:80>
        ServerName repo.example.com
        ServerAdmin admin@example.com
        DocumentRoot /var/www/html/repo/

        <Directory /var/www/html/repo>
            Options +Indexes +FollowSymLinks
            AllowOverride All
            Require all granted
        </Directory>

        ErrorLog ${APACHE_LOG_DIR}/solder_repo_error.log
        CustomLog ${APACHE_LOG_DIR}/solder_repo_access.log combined
    </VirtualHost>
    ```

## Apache Rewrite Engine

Laravel ships with a `public/.htaccess` file that uses `mod_rewrite` to route all requests through `index.php`. For this to work, two things must be in place:

1. **Enable `mod_rewrite`:**

    ```bash
    sudo a2enmod rewrite
    sudo systemctl restart apache2
    ```

2. **Set `AllowOverride All`** in the `<Directory>` block for Solder's `public/` directory (already included in the vhost above).

!!! warning
    Without `mod_rewrite` and `AllowOverride All`, Laravel's routing will not work. All URLs will require `/index.php` in the path (e.g. `solder.example.com/index.php/dashboard` instead of `solder.example.com/dashboard`).

## HTTPS

All production deployments should use HTTPS. There are two common approaches.

### Certbot / Let's Encrypt

The simplest option is to use [Certbot](https://certbot.eff.org/) to obtain a free TLS certificate from Let's Encrypt:

```bash
# nginx
sudo certbot --nginx -d solder.example.com -d repo.example.com

# Apache
sudo certbot --apache -d solder.example.com -d repo.example.com
```

Certbot will automatically modify your server blocks / vhosts to listen on port 443 and redirect HTTP to HTTPS.

### Reverse Proxy / Load Balancer

If Solder sits behind a reverse proxy or load balancer that terminates TLS, the application itself runs on plain HTTP but needs to know that external traffic is encrypted. Configure two things:

1. **Set `APP_URL` to `https://`** in your `.env` file:

    ```ini
    APP_URL=https://solder.example.com
    ```

2. **Tell PHP that HTTPS is active.** In nginx, uncomment the `fastcgi_param` line in the Solder server block:

    ```nginx
    fastcgi_param HTTPS on;
    ```

    For Apache, add the following inside your `<VirtualHost>` block:

    ```apache
    SetEnvIf X-Forwarded-Proto https HTTPS=on
    ```

!!! tip
    Make sure your reverse proxy forwards the `X-Forwarded-For` and `X-Forwarded-Proto` headers so Laravel can correctly detect the client IP and protocol. Laravel's `TrustProxies` middleware handles this automatically when configured.
