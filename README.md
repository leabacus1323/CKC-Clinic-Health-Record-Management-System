# 🏥 CKC Clinic Health Record Management System (CKC Clinic HRMS)

A comprehensive Progressive Web App (PWA) for managing student health records, appointments, prescriptions, and medical examinations.

## ✨ Features

- 📱 **Progressive Web App** - Install as native app on mobile/desktop
- 👨‍⚕️ **Doctor Dashboard** - Manage appointments, prescriptions, and patient records
- 💊 **Medicine Management** - Track inventory, stock levels, and expiration dates
- 📋 **Prescription System** - Prescribe, update, and track medication history
- 🏥 **Health Records** - Dental, physical exams, and immunization tracking
- 📊 **Reports & Analytics** - Patient statistics and medical reports
- 🔐 **Secure Authentication** - Role-based access control

---

## 📋 Prerequisites

Choose **ONE** installation method:

### Option A: Local Development (Recommended for Development)
- **PHP** >= 8.1 ([Download](https://www.php.net/downloads))
- **Composer** ([Download](https://getcomposer.org/download/))
- **Node.js** >= 16.x and NPM ([Download](https://nodejs.org/))
- **SQLite** (comes with PHP) or **MySQL**
- **Git** ([Download](https://git-scm.com/downloads))

### Option B: Docker Deployment (Recommended for Production)
- **Docker Desktop** ([Download](https://www.docker.com/products/docker-desktop/))
- **Docker Compose** (included with Docker Desktop)
- **Git** ([Download](https://git-scm.com/downloads))

---

## 🚀 Installation

### **Option A: Local Development Setup**

#### Step 1: Clone the Repository
```bash
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord
```

#### Step 2: Install PHP Dependencies
```bash
composer install
```

#### Step 3: Configure Environment
```bash
# Windows (PowerShell)
copy .env.example .env

# Linux/Mac
cp .env.example .env
```

Edit `.env` file and configure:
```env
APP_NAME="CKC Clinic Health Record Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite
# For SQLite, create database file:
# Windows: type nul > database\database.sqlite
# Linux/Mac: touch database/database.sqlite

# OR use MySQL:
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=ckc_clinic
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

#### Step 4: Generate Application Key
```bash
php artisan key:generate
```

#### Step 5: Create Database File (if using SQLite)
```bash
# Windows (PowerShell)
New-Item database\database.sqlite -ItemType File

# Linux/Mac
touch database/database.sqlite
```

#### Step 6: Run Database Migrations
```bash
php artisan migrate
```

#### Step 7: Seed Database (Optional - for test data)
```bash
php artisan db:seed
```

#### Step 8: Create Storage Link
```bash
php artisan storage:link
```

#### Step 9: Install Node Dependencies & Build Assets
```bash
npm install
npm run build
```

#### Step 10: Publish PWA Assets
```bash
php artisan laravel-pwa:publish
```

#### Step 11: Start Development Server
```bash
php artisan serve
```

🎉 **Access the application at:** `http://localhost:8000`

---

### **Option B: Docker Deployment**

#### Step 1: Clone the Repository
```bash
git clone https://github.com/Ledeuadian/CTCclinicrecord.git
cd CTCclinicrecord
```

#### Step 2: Configure Environment
```bash
cp .env.example .env
```

Edit `.env` file and update database credentials to match `docker-compose.yml`:
```env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=ckc_clinic
DB_USERNAME=ckc_user
DB_PASSWORD=your_secure_password
```

#### Step 3: Install Dependencies
```bash
composer install
npm install
npm run build
```

#### Step 4: Generate Application Key
```bash
php artisan key:generate
```

#### Step 5: Build & Start Docker Containers
```bash
# Linux/Mac
sudo docker-compose up -d --build

# Windows (PowerShell as Admin)
docker-compose up -d --build
```

#### Step 6: Run Migrations Inside Container
```bash
# Linux/Mac
sudo docker-compose exec app php artisan migrate

# Windows
docker-compose exec app php artisan migrate
```

#### Step 7: Seed Database (Optional)
```bash
# Linux/Mac
sudo docker-compose exec app php artisan db:seed

# Windows
docker-compose exec app php artisan db:seed
```

#### Step 8: Create Storage Link
```bash
# Linux/Mac
sudo docker-compose exec app php artisan storage:link

# Windows
docker-compose exec app php artisan storage:link
```

🎉 **Access the application at:** `http://localhost` (or your configured port)

---

## 🔧 Common Issues & Fixes

### Permission Denied (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### PHP Extensions Missing
Uncomment in `php.ini`:
```ini
extension=zip
extension=fileinfo
extension=pdo_sqlite
extension=gd
```

### Clear Cache Issues
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 🐳 Docker Commands Reference

```bash
# Start containers
docker-compose start

# Stop containers
docker-compose stop

# Stop and remove containers
docker-compose down -v

# View logs
docker-compose logs -f app

# Access container shell
docker-compose exec app bash

# Clear cache inside container
docker-compose exec app php artisan cache:clear
docker-compose exec app php artisan config:clear
docker-compose exec app php artisan view:clear
docker-compose exec app php artisan route:clear
```

---

## 📱 PWA Installation (For End Users)

### On Mobile (Android/iOS)
1. Open the clinic system in Chrome/Safari
2. Tap the menu (⋮) or share button
3. Select "Add to Home Screen"
4. Launch from home screen like a native app

### On Desktop (Chrome/Edge)
1. Open the clinic system in browser
2. Click the install icon (⊕) in the address bar
3. Click "Install"
4. Launch from desktop/start menu

---

## 👤 Default Login Credentials

After seeding, use these credentials (if seeders are configured):
```
Email: admin@ckc.edu
Password: password
```

> ⚠️ **Change default passwords immediately in production!**

---

## 🛠️ Development Commands

```bash
# Start development server with hot reload
npm run dev
php artisan serve

# Build for production
npm run build

# Run tests
php artisan test

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models

# Fresh migration (⚠️ Deletes all data)
php artisan migrate:fresh --seed
```

---

## 📁 Project Structure

```
ckc-shrms-master/
├── app/
│   ├── Http/Controllers/     # Controller logic
│   ├── Models/               # Eloquent models
│   └── View/Components/      # Blade components
├── database/
│   ├── migrations/           # Database migrations
│   └── seeders/              # Database seeders
├── public/
│   ├── manifest.json         # PWA manifest
│   └── sw.js                 # Service worker
├── resources/
│   ├── views/                # Blade templates
│   ├── css/                  # Stylesheets
│   └── js/                   # JavaScript
└── routes/
    └── web.php               # Web routes
```

---

## 🤝 Contributing

Contributions are welcome! Please follow these steps:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## 📞 Support

For issues, questions, or contributions:
- **Repository**: [github.com/Ledeuadian/CTCclinicrecord](https://github.com/Ledeuadian/CTCclinicrecord)
- **Issues**: [Submit an issue](https://github.com/Ledeuadian/CTCclinicrecord/issues)

---

**Built with ❤️ using Laravel & Tailwind CSS**


## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
