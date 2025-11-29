#!/bin/bash

# =============================
#  XAMPP STYLE PODMAN GUI
#  MariaDB + phpMyAdmin + Nginx + PHP
# =============================

SERVICE_DB="podman-mariadb"
SERVICE_PMA="podman-phpmyadmin"
SERVICE_PHP="podman-php"
SERVICE_NGINX="podman-nginx"

ALL_SERVICES="$SERVICE_DB $SERVICE_PMA $SERVICE_PHP $SERVICE_NGINX"

while true; do
choice=$(zenity --list --title="Podman Control Panel" \
        --text="MariaDB + phpMyAdmin + Web Server Control Panel" \
        --column="Action" --width=350 --height=400 \
        "Start All Services" \
        "Stop All Services" \
        "Restart All Services" \
        "Status" \
        "Open phpMyAdmin" \
        "Open Website" \
        "Exit")

case $choice in
    "Start All Services")
        podman start $ALL_SERVICES
        zenity --info --text="All services started!"
        ;;

    "Stop All Services")
        podman stop $ALL_SERVICES
        zenity --warning --text="All services stopped!"
        ;;

    "Restart All Services")
        podman restart $ALL_SERVICES
        zenity --info --text="All services restarted!"
        ;;

    "Status")
        STATUS_OUTPUT=$(podman ps --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}" | \
                        grep -E "podman-" || echo "No services running.")
        zenity --info --width=550 --height=350 --text="Current Status:\n\n$STATUS_OUTPUT"
        ;;

    "Open phpMyAdmin")
        xdg-open http://localhost:8080 2>/dev/null
        ;;

    "Open Website")
        xdg-open http://localhost:8000 2>/dev/null
        ;;

    "Exit")
        exit 0
        ;;
esac
done

