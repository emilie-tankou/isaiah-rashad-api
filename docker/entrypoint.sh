#!/bin/sh
# Exécuté à chaque démarrage du conteneur.
set -e
cd /var/www/html

# Les clés JWT ne sont jamais dans Git : on les crée au premier démarrage.
# (Sur l'offre gratuite le disque est effacé à chaque redéploiement : les anciens jetons
#  deviennent alors invalides et l'admin se reconnecte, c'est sans conséquence.)
if [ ! -f config/jwt/private.pem ]; then
    runuser -u www-data -- php bin/console lexik:jwt:generate-keypair --skip-if-exists --no-interaction
fi

runuser -u www-data -- php bin/console cache:warmup

# Crée ou met à jour les tables dans la base (Neon)
runuser -u www-data -- php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

exec apache2-foreground