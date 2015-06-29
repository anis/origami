[![Build Status](https://travis-ci.org/anis/origami.svg)](https://travis-ci.org/anis/origami)

# Origami

Origami est un outil simple de gestion administrative pour freelances en EURL. Origami vous permet d'établir votre comptabilité client, suivre vos paiements, calculer et prévoir vos cotisations sociales et impositions, ou encore optimiser votre revenu personnel.

## Comment contribuer ?

### Dépendances techniques

Vous devez disposer préalablement sur votre machine :

* de PHP 5.5 ou supérieur
* d'un serveur MySQL
* de composer
* (optionnel) d'un serveur Apache 2 avec l'extension PHP et le module rewrite actifs

### Déploiement local

* Clonez le projet

```sh
git clone https://github.com/anis/origami
```

* Installez les dépendances logicielles

```sh
composer install
```

* Créez l'ossature de la base de données

```sh
mysql -h localhost -u root
```
```sql
CREATE DATABASE `origami`;
exit;
```
```sh
./vendor/bin/doctrine orm:schema-tool:create
```

* Lancez les tests unitaires et assurez-vous que tout est au vert

```bash
./vendor/bin/atoum -d ./tests/unit/ -bf ./tests/unit/.bootstrap.atoum.php
```

Vous pouvez également souhaiter tester l'API via un navigateur ou n'importe quel client HTTP, vous pouvez alors créer un VirtualHost qui pointe sur le dossier "./public" et qui autorise les .htaccess.

### Envoi d'une contribution

Pour contribuer à Origami, veuillez respecter les étapes et règles suivantes :

* créez un fork du projet avec votre compte Github
* créez une branche respectant la nomenclature suivante : (new|fix|evo)_name. Où **new** correspond à une nouvelle feature, **fix** à un correctif, et **evo** à une évolution d'une feature existante
* établissez vos commits sur cette branche en faisant des sommaires très courts commençant par "Add", "Fix", ou "Change", et des descriptions aussi longues que vous le souhaitez
* pushez et faites votre pull request
