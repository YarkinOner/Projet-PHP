# 🎮 GameRelic – E-commerce PHP Project

## 📌 Description

GameRelic est une application web développée en PHP permettant aux utilisateurs d’acheter et de vendre des figurines de jeux vidéo, d’anime et de films à travers une interface moderne et intuitive.

Le projet a été réalisé dans le cadre d’un examen de développement web et fonctionne entièrement en local via un environnement XAMPP.

L’objectif est de proposer une expérience utilisateur fluide pour la publication, la gestion et l’achat de figurines, tout en garantissant un système d’authentification sécurisé, une gestion dynamique du stock et un panier fonctionnel.

Le site permet aux utilisateurs de :

- 👤 Créer un compte / Se connecter
- 🛒 Ajouter des figurines au panier
- 💰 Valider une commande
- 📦 Gestion automatique du stock
- 🏷️ Publier leurs propres annonces
- ❌ Supprimer leurs annonces
- 🖼️ Ajouter une image lors de la publication

---

## 🛠️ Technologies utilisées

- PHP 8
- MySQL
- XAMPP
- HTML5
- Git / GitHub

---

## 🗄️ Base de données

Nom de la base de données :

php_exam_db

Tables principales :

- users
- articles
- stock
- cart
- invoice

Relations :

- articles.author_id → users.id
- stock.article_id → articles.id
- cart.user_id → users.id
- cart.article_id → articles.id

---

## 🚀 Installation (Windows – XAMPP)

1. Installer XAMPP
2. Copier le dossier `php_exam` dans :

C:\xampp\htdocs\

3. Lancer Apache + MySQL
4. Aller sur :

http://localhost/phpmyadmin

5. Importer le fichier `php_exam_db.sql`
6. Lancer le site :

http://localhost/php_exam/public/

---

## ✨ Fonctionnalités principales

### 🔐 Authentification
- Inscription sécurisée
- Connexion utilisateur
- Gestion des sessions

### 🏪 Vente d’articles
- Publication d’une figurine
- Upload d’image (jpg, png, webp)
- Gestion du stock
- Suppression de ses propres articles

### 🛒 Panier
- Ajout avec vérification du stock
- Mise à jour de la quantité
- Suppression d’un article
- Calcul total automatique

### 💳 Commande
- Déduction automatique du stock
- Déduction du solde utilisateur
- Création d’une facture

---

## 🎨 Interface

- Thème dark moderne
- Responsive (compatible mobile)
- Design cohérent sur toutes les pages

---

### 📁 Structure du projet

```
php_exam/
│
├── config/
│   ├── db.php
│   └── auth.php
│
├── public/
│   ├── index.php
│   ├── detail.php
│   ├── sell.php
│   ├── cart.php
│   ├── login.php
│   ├── register.php
│   ├── account.php
│   ├── delete_article.php
│   ├── validate.php
│   └── assets/
│       └── style.css
│
├── uploads/
│
└── php_exam_db.sql
```


---

## 👥 Auteurs

Projet réalisé par :

- Yarkin ONER
- Nathan CHAMPILOU



## 📌 Remarque

Le projet est entièrement fonctionnel en local via XAMPP.

Les images uploadées sont stockées dans le dossier `uploads/` à la racine du projet.

---
