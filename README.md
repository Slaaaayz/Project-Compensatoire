## Pour lancer le projet, il faut d'abord lancer la commande suivante :

Remplacer la clé API dans le fichier NbaApiService.php par votre clé API.

```bash
Projet/src/Service/NbaApiService.php 
private const API_KEY = 'YOUR_API_KEY';
```

```bash
cd Projet/
composer install
```

Ensuite, il faut lancer la commande suivante :



```bash
php bin/console doctrine:database:create
symfony server:start
```
Puis, aller sur le lien suivant :

```bash
http://localhost:8000
```

## Et pour voir le blog, il faut aller sur le lien suivant :

```bash
https://blog.slayz.fr/
```