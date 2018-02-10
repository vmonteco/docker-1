#!/usr/bin/env sh

# Directories
mkdir -p media/img

# Create database
#mysql -u root -e "CREATE DATABASE IF NOT EXISTS camagru CHARACTER SET 'utf8';" -p

php -S localhost:8000 index.php
