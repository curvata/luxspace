![LUXSPACE](/.github/intro.png)

# LUXSPACE ✦ Réservation de vols spatiaux

Application web fictive de réservation de vols pour l'espace, réalisée dans le cadre d'un portfolio personnel.

---

## Installation

**1. Cloner le dépôt**
```bash
git clone https://github.com/curvata/luxspace.git
cd luxspace
```

**2. Installer les dépendances**
```bash
composer install
npm install
```

**3. Configurer l'environnement**

Créer un fichier `.env.local` à la racine :
```dotenv
APP_SECRET=votre_secret
DATABASE_URL="mysql://user:password@127.0.0.1:3306/luxspace?serverVersion=8.3.0"
MAILER_DSN=smtp://localhost:1025
MY_MAIL=noreply@votredomaine.com
MY_DOMAIN=http://localhost:8000
STRIPE_PRIVATE=sk_test_...
STRIPE_PUBLIC=pk_test_...
```

**4. Compiler les assets**
```bash
npm run build
```

**5. Base de données**
```bash
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load --group=ProdFixtures
```

## Tests

```bash
php bin/phpunit

# Code coverage
XDEBUG_MODE=coverage php bin/phpunit --coverage-html coverage
```
