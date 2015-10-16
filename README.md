# Fruit Dashboard

Fruit Dashboard is a Chrome dashboard solution for startup companies.

### How to build your local development box?
  - download & install [Virtualbox](https://www.virtualbox.org/)
  - download & install [Vagrant](https://www.vagrantup.com/)
  - download & install [Github for Windows](https://windows.github.com/) or [Github for Mac](https://mac.github.com/)

### Clone the vagrant lamp server
  - ```git clone https://github.com/tryfruit/vagrant-ubuntu-14-04-lamp```

### Install the vagrant virtual environment
  - ```cd vagrant-ubuntu-14-04-lamp```
  - ```vagrant up```
  - (...wait until the installer finishes)

### Log in to the vagrant virtual environment
  - ```vagrant ssh```

### Install Fruit-dashboard and necessary packages
  - ```sh /var/www/_install/fruit-dashboard.sh```
  - (...wait until the installer finishes)

### Run the laravel server
  - ```cd /var/www/fruit-dashboard```
  - ```sh serve```

### Check the site in your browser
  - Open ```http://localhost:8001/```

### The installer made you some aliases that may come handy
  - ```alias fds='cd /var/www/fruit-dashboard/'```
  - ```alias fdc='cd /var/www/fruit-dashboard-config/'```
  - ```alias fdd='mysql -u root -ppassword fruitdashboarddb'```
  - ```alias fdserve='cd /var/www/fruit-dashboard/;sh serve;'```
  - ```alias fdlog='cd /var/www/fruit-dashboard/app/storage/logs/; tail -f $(ls -t * | head -1);'```

**...aaaaaand you are done. :)**
