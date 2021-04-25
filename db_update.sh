bin/console doctrine:schema:drop -f
bin/console doctrine:schema:create
bin/console doctrine:fixtures:load -n
