#!/bin/bash

# ~~~~~~~~~~~~ CONFIG ~~~~~~~~~~~~ #
# --------- Source config -------- #
ROOT_FOLDER='/var/www/fruit-dashboard'
# --------- MySQL config --------- #
MYSQL_ROOT_USER='root'
MYSQL_ROOT_PASS='password'
MYSQL_DB='fruitdashboarddb'
MYSQL_DB_USER='FDRoot'
MYSQL_DB_PASS='FDPassword'

echo "# ------ Resetting database ------ #"
# Drop database
mysql -u `echo $MYSQL_ROOT_USER` -p`echo $MYSQL_ROOT_PASS` -e "DROP DATABASE IF EXISTS $MYSQL_DB;"
# Create new database
mysql -u `echo $MYSQL_ROOT_USER` -p`echo $MYSQL_ROOT_PASS` -e "CREATE DATABASE IF NOT EXISTS $MYSQL_DB;"
# Grant permissions
mysql -u `echo $MYSQL_ROOT_USER` -p`echo $MYSQL_ROOT_PASS` -e "GRANT ALL ON $MYSQL_DB.* TO '$MYSQL_DB_USER'@'localhost' IDENTIFIED BY '$MYSQL_DB_PASS';"
echo "# --- Done: Resetting database --- #"

echo "# ------ Migrating database ------ #"
cd `echo $ROOT_FOLDER`
php artisan migrate
echo "# --- Done: Migrating database --- #"

echo "# ------- Creating DB seeds ------- #"
php artisan db:seed
echo "# ---- Done: Creating DB seeds ---- #"
