# A défaut d'une technique plus propre interne aux tests,
# on recrée la base de données from scratch afin d'avoir les AI à 1
# Mettre le droit d'éxécution sur le fichier via `chmod +x ./test-fixtures.sh`
# Exécuter le script via `./test-fixtures.sh`
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
echo "Executing migrations in quiet mode..."
php bin/console doctrine:migrations:migrate -q
php bin/console doctrine:fixtures:load --no-interaction
#php bin/phpunit tests/SmokeTest.php