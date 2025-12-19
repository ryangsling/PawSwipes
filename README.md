# PawSwipes - Responsive Web Application

## Project Overview
PawSwipes is a responsive web application that allows users to register, login, and manage their profiles to find perfect matches for their dogs. This is a "Tinder for dogs" concept with complete user authentication, profile management, and CRUD functionality.

## Technologies Used
- **Frontend**: HTML, CSS, Bootstrap 5.3, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Email**: PHPMailer
- **Features**: Responsive design, Dark/Light mode toggle

## Installation Instructions

### 1. Database Setup
1. Create a MySQL database named `pawswipes_db`
2. Import the `database.sql` file to create the required tables:
   ```sql
   mysql -u your_username -p pawswipes_db < database.sql
   ```

### 2. PHP Dependencies
Install PHPMailer using Composer:
```bash
composer require phpmailer/phpmailer
```

### 3. Configuration
1. Update `config.php` with your database credentials:
   ```php
   $host = 'localhost';
   $dbname = 'pawswipes_db';
   $username = 'your_db_username';
   $password = 'your_db_password';
   ```

2. Configure email settings in `register.php`:
   ```php
   $mail->Host = 'your_smtp_host';
   $mail->Username = 'your_email@domain.com';
   $mail->Password = 'your_app_password';
   ```

### 4. File Structure
```
pawswipes/
├── index.html (original landing page)
├── style.css (original styles)
├── enhanced-style.css (enhanced styles with dark mode)
├── config.php (database configuration)
├── login.php (login form)
├── register.php (registration form)
├── verify.php (email verification)
├── dashboard.php (user dashboard)
├── profile.php (profile management)
├── add_dog.php (add dog form)
├── script.js (JavaScript functionality)
├── logout.php (logout handler)
├── database.sql (database schema)
└── uploads/ (image uploads directory)
    ├── profiles/ (user profile images)
    └── dogs/ (dog images)
```

## Features Implemented

### 1. User Authentication ✅
- Registration with email verification
- Login with session management
- Secure password hashing
- Email verification using PHPMailer

### 2. Responsive Design ✅
- Bootstrap 5.3 for mobile-first design
- Responsive navigation
- Mobile-optimized forms
- Flexible grid layouts

### 3. Profile Management (CRUD) ✅
- **Create**: User registration and dog profiles
- **Read**: View user dashboard and profiles
- **Update**: Edit user profile and dog information
- **Delete**: Remove dog profiles

### 4. Front-end & Back-end Validation ✅
- JavaScript form validation
- PHP server-side validation
- Real-time password confirmation
- File upload validation

### 5. Dark/Light Mode Toggle ✅
- Bootstrap 5.3 color modes
- Persistent theme storage
- Smooth transitions
- Theme-aware components

### 6. Additional Features ✅
- Image upload for profiles and dogs
- Session handling
- Error and success messaging
- Secure file uploads
- Auto-hiding alerts

## Database Schema

### Users Table
- `id` (Primary Key)
- `first_name`, `last_name`
- `email` (Unique)
- `password` (Hashed)
- `phone`, `profile_image`, `bio`, `location`
- `verification_token`, `is_verified`
- `created_at`, `updated_at`

### Dogs Table
- `id` (Primary Key)
- `user_id` (Foreign Key)
- `name`, `breed`, `age`, `gender`, `size`
- `description`, `image`
- `is_active`
- `created_at`, `updated_at`

### Matches Table (Future Implementation)
- Ready for swiping functionality
- Tracks likes/passes between dogs
- Supports mutual matching

## Security Features
- Password hashing with `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- File upload security with type and size validation
- Session management
- Email verification for account activation

## Usage Instructions

1. **Setup**: Follow installation instructions above
2. **Access**: Navigate to the application in your web browser
3. **Register**: Create a new account with email verification
4. **Login**: Access your dashboard after email verification
5. **Profile**: Update your personal information and photo
6. **Dogs**: Add your dogs with photos and descriptions
7. **Theme**: Toggle between dark and light modes

## Future Enhancements
- Swiping functionality implementation
- Match notifications
- Chat system for matched users
- Geolocation-based matching
- Advanced search filters
- Mobile app development

## File Permissions
Ensure the following directories are writable:
```bash
chmod 755 uploads/
chmod 755 uploads/profiles/
chmod 755 uploads/dogs/
```

## Troubleshooting

### Email Not Sending
- Check SMTP configuration in `register.php`
- Verify email credentials and app passwords
- Ensure firewall allows SMTP connections

### Database Connection Issues
- Verify database credentials in `config.php`
- Check if MySQL service is running
- Confirm database and tables exist

### File Upload Issues
- Check directory permissions for `uploads/` folder
- Verify PHP upload settings (`upload_max_filesize`, `post_max_size`)
- Ensure proper file type validation

This implementation provides a complete responsive web application with user authentication, profile management, and all requested features using PHP, MySQL, Bootstrap, and JavaScript.