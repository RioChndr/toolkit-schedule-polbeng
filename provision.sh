# update package lists
sudo apt-get update

# install php and extensions
sudo apt-get install -y php php-cli php-common php-curl php-mbstring php-mysql php-xml php-dom php-mysqli php-json php-exif php-fileinfo php-hash php-imagick php-mbstring php-openssl php-pcre php-xml php-zip php-ftp php-fpm
sudo apt-get install -y composer

# install nginx
sudo apt-get install -y nginx

# configure nginx
sudo rm /etc/nginx/sites-enabled/default
sudo touch /etc/nginx/sites-available/myapp
sudo ln -s /etc/nginx/sites-available/myapp /etc/nginx/sites-enabled/
sudo echo "server {
    listen 80;
    server_name localhost;
    root /var/www/html;
    index index.php index.html index.htm;
    location / {
    try_files \$uri \$uri/ /index.php?\$query_string;
    }
    location ~ \.php$ {
    try_files \$uri /index.php =404;
    fastcgi_split_path_info ^(.+\.php)(/.+)$;
    fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME \$document_root\$fastcgi_script_name;
    fastcgi_param PATH_INFO       \$fastcgi_path_info;
    include fastcgi_params;
    }
}" | sudo tee /etc/nginx/sites-available/myapp


# restart nginx
sudo systemctl restart nginx