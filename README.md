# Salon de coiffure

Projet de site de gestion d'un salon de coiffure.

## Installation de la base de données

Le fichier `salon_coiffure.sql` décrit les tables, mais ne crée pas lui-même
la base de données. Créez-la puis importez le schéma :

```sql
CREATE DATABASE salon_coiffure
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
```

```sh
mysql -u root -p salon_coiffure < salon_coiffure.sql
```

Créez un utilisateur MySQL dédié à l'application plutôt que d'utiliser `root`.
La page actuelle ne fait que lire les prestations ; cet utilisateur n'a donc
besoin que du droit `SELECT` :

```sql
CREATE USER 'salon_app'@'localhost' IDENTIFIED BY 'choisir-un-mot-de-passe-long';
GRANT SELECT ON salon_coiffure.prestation TO 'salon_app'@'localhost';
```

## Configuration et lancement

La connexion PDO dans `db.php` lit ces variables d'environnement :

- `DB_HOST` (par défaut `127.0.0.1`)
- `DB_PORT` (par défaut `3306`)
- `DB_NAME`
- `DB_USER`
- `DB_PASSWORD`

Ne placez pas le mot de passe dans le code ou dans un fichier versionné.
Exemple de lancement local depuis ce dossier :

```sh
DB_HOST=127.0.0.1 DB_PORT=3306 DB_NAME=salon_coiffure \
DB_USER=salon_app DB_PASSWORD='votre-mot-de-passe' \
php -S 127.0.0.1:8000
```

Ouvrez ensuite `http://127.0.0.1:8000`. La page affiche les prestations
enregistrées dans la table `prestation`.

## Protections en place

- Accès à la base via PDO en `utf8mb4`, exceptions activées et émulation des
  requêtes préparées désactivée.
- Aucun secret de connexion n'est enregistré dans le dépôt.
- Le contenu de la base affiché dans le HTML est échappé pour prévenir les XSS.
- Les erreurs techniques sont consignées dans le journal PHP et ne sont pas
  révélées au visiteur.
- En-têtes HTTP de protection contre le clickjacking, le sniffing MIME et
  les chargements de ressources non autorisés.

Pour les futures opérations d'écriture, utilisez également des requêtes
préparées, validez les données côté serveur et protégez les formulaires avec
un jeton CSRF. En production, servez le site exclusivement en HTTPS et
configurez PHP pour ne pas afficher les erreurs aux visiteurs.
