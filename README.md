# PHP Advanced CRUD and Authentication System

Bu loyiha professional PHP web-ilova bo'lib, foydalanuvchi autentifikatsiyasi, profil boshqaruvi, to'liq CRUD, qidiruv, saralash va pagination imkoniyatlarini birlashtiradi. Joriy muhitda qo'shimcha database driver talab qilinmasligi uchun ma'lumotlar JSON storage fayllarida saqlanadi.

## Arxitektura

- JSON file storage bilan ishlaydi: `storage/users.json` va `storage/posts.json`.
- Sessiya asosidagi autentifikatsiya ishlatiladi.
- CSRF himoyasi barcha muhim formalar va o'chirish amallariga qo'shilgan.
- XSS xavfini kamaytirish uchun barcha chiqishlar `htmlspecialchars()` bilan ekranga chiqariladi.
- Yozuvlar foydalanuvchi egaligi bo'yicha cheklanadi, ya'ni har bir user faqat o'z postlarini ko'radi va boshqaradi.

## Funksional imkoniyatlar

- Register, login va logout.
- User profile ko'rish va tahrirlash.
- Parolni current password orqali almashtirish.
- Post yaratish, ko'rish, tahrirlash va o'chirish.
- Search: sarlavha va matn bo'yicha qidiruv.
- Sort: sana, sarlavha, kategoriya va holat bo'yicha.
- Filter: kategoriya va holat bo'yicha.
- Pagination: postlar sahifalarga bo'lingan holda chiqariladi.

## Loyiha struktura

```text
/web_project
├── assets/
│   ├── css/style.css
│   └── js/app.js
├── auth/
│   ├── login.php
│   ├── logout.php
│   └── register.php
├── config/
│   └── database.php
├── includes/
│   ├── footer.php
│   ├── functions.php
│   └── header.php
├── storage/
│   ├── posts.json
│   └── users.json
├── user/
│   ├── edit_profile.php
│   └── profile.php
├── create.php
├── delete.php
├── index.php
├── update.php
├── view.php
├── database.sql
└── database.sqlite
```

## TZ struktura

1. Authentication moduli
   Register, login, logout va session management.

2. User profile moduli
   Profilni ko'rish, foydalanuvchi ma'lumotlarini tahrirlash va parolni yangilash.

3. CRUD moduli
   Postlar uchun create, read, update va delete amallari.

4. Search and sort moduli
   Qidiruv, filtr, saralash va pagination bitta dashboard ichida ishlaydi.

5. Security qatlami
   CSRF token, prepared statements, XSS escaping va ownership check.

## O'rnatish

1. Serverda PHP o'rnatilgan bo'lishi kerak.
2. Loyiha papkasida quyidagi buyruq bilan local serverni ishga tushiring:

```bash
php -S localhost:8000
```

3. Brauzerda `http://localhost:8000` ni oching.
4. Ilova birinchi ishga tushganda `storage/users.json` va `storage/posts.json` fayllarini avtomatik yaratadi.

## Storage sxema

`database.sql` fayli avvalgi SQL modelni hujjatlashtirish uchun qoldirilgan, amaldagi runtime esa JSON storage bilan ishlaydi.

## Eslatma

Joriy muhitda `php` CLI mavjud emasligi sababli serverni shu sessiyada terminaldan ishga tushirib tekshirib bo'lmadi. Fayllar editor diagnostikasi orqali tekshirildi.