# 🏛️ CampusMaster Backend - API REST

Le socle technique de **CampusMaster**, une plateforme de gestion académique. Ce backend est construit avec **Laravel 11**, en suivant les principes de la **Clean Architecture** et sécurisé par **JWT**.

## 🛠️ Spécificités Techniques

* **Authentification** : JWT (JSON Web Token) via `tymon/jwt-auth`.
* **Architecture** : Pattern Repository & Services (Découplage complet).
* **Sécurité** : RBAC (Role-Based Access Control) personnalisé.
* **Documentation** : Swagger (OpenAPI 3.0).

---

## 📦 Installation

### 1. Prérequis

* PHP 8.2+
* Composer
* MySQL 8.0+

### 2. Initialisation du projet

```bash
# Cloner le projet
git clone [URL_DU_REPO]
cd campus-master-backend

# Installer les dépendances
composer install

```

### 3. Configuration de l'environnement (`.env`)

Copiez le fichier d'exemple et générez les clés de sécurité :

```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret

```

#### Configuration de la Base de Données

Éditez votre `.env` avec vos identifiants :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=campus_master_db
DB_USERNAME=root
DB_PASSWORD=

```

#### Configuration de l'envoi de mail

Essentiel pour les notifications et les mots de passe oubliés (Exemple avec Mailtrap) :

```env
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=votre_identifiant
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@campusmaster.sn"
MAIL_FROM_NAME="${APP_NAME}"

```

### 4. Migration de la base de données

```bash
php artisan migrate

```

---

## 🔑 Création de l'administrateur

Une commande personnalisée est disponible pour initialiser le système en toute sécurité :

```bash
php artisan app:create-admin

```

*Suivez les instructions dans le terminal pour définir le nom, l'email et le mot de passe de l'administrateur principal.*

---

## Executer le projet


```bash
php artisan serve
```

## 🏗️ Structure du Projet (Clean Architecture)

Le dossier `app/` est organisé pour séparer les préoccupations :

* **`Core/Domain/Entities`** : Contient les modèles Eloquent et les constantes métier.
* **`Core/Application/Services`** : Contient la logique applicative (ex: `GradeService`, `ModuleService`).
* **`Core/Infrastructure/Repositories`** : Gère l'accès direct aux données (Requêtes optimisées).
* **`Http/Controllers`** : Contrôleurs API chargés de la validation et des réponses JSON.
