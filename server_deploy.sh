(php artisan down --message 'La aplicación se está actualizando. Vuelva a intentarlo en un minuto.') || true

  php artisan migrate --force

php artisan up

echo "Aplicación desplegada";