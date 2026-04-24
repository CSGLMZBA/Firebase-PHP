# Firebase-PHP

PHP web application with Firebase backend integration, featuring user authentication, JWT tokens, and Firestore database.

## Features

- **User Authentication**: Secure login system with JWT tokens
- **Firebase Integration**: Uses Firebase Auth and Firestore for data storage
- **RESTful API**: Clean API endpoints for user management
- **Responsive Frontend**: Bootstrap-based UI for dashboard and user management
- **Session Management**: PHP sessions with token-based authentication
- **Admin User Creation**: Script to bootstrap admin user

## Prerequisites

- PHP 8.0 or higher
- Apache web server with mod_rewrite enabled
- Composer (for dependency management, if needed)
- Firebase project with Firestore enabled
- Service account key from Firebase

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/CSGLMZBA/Firebase-PHP.git
   cd Firebase-PHP
   ```

2. **Set up Apache:**
   - Point your document root to `/path/to/Firebase-PHP`
   - Ensure `mod_rewrite` is enabled
   - Configure virtual host if needed
   - **Note**: Apache must serve from the Firebase-PHP folder. If `index.php` is set as the default landing page in `dir.conf`, accessing the root URL will automatically redirect to the login page in the public `index.php` file

3. **Firebase Setup:**
   - Create a Firebase project at [Firebase Console](https://console.firebase.google.com)
   - Enable Firestore Database
   - Create a service account and download the JSON key

4. **Configuration:**
   - Copy your Firebase service account JSON data to `backend/app/config/firebase_credentials.php`
   - The file should return an array with `project_id`, `client_email`, and `private_key`

5. **Create Admin User:**
   ```bash
   php backend/scripts/create_admin.php
   ```
   This creates an admin user with username `Admin` and password `Password`.

## Usage

### Frontend Access

- **Login Page**: `http://your-domain/Frontend/public/index.php`
- **Dashboard**: `http://your-domain/Frontend/public/dashboard.php` (requires login)
- **User Management**: `http://your-domain/Frontend/public/users.php` (requires login)

### API Endpoints

All API endpoints are prefixed with `/backend/public/index.php/api/`

#### Authentication
- `POST /api/auth/login` - User login
  ```json
  {
    "usuario": "username",
    "password": "password"
  }
  ```

#### User Management (requires authentication)
- `GET /api/users` - List all active users
- `POST /api/users` - Create new user
- `PATCH /api/users/{id}` - Update user
- `PATCH /api/users/{id}/toggle-active` - Toggle user active status
- `DELETE /api/users/{id}` - Delete user (soft delete)

## Project Structure

```
Firebase-PHP/
├── backend/
│   ├── app/
│   │   ├── config/
│   │   │   ├── app.php
│   │   │   ├── firebase.php
│   │   │   └── firebase_credentials.php (ignored)
│   │   ├── controllers/
│   │   ├── helpers/
│   │   ├── middlewares/
│   │   ├── repositories/
│   │   ├── routes/
│   │   ├── schemas/
│   │   └── services/
│   ├── public/
│   │   ├── index.php
│   │   └── .htaccess
│   └── scripts/
├── Frontend/
│   └── public/
│       ├── index.php
│       ├── dashboard.php
│       ├── users.php
│       ├── logout.php
│       ├── api.php
│       └── assets/
└── login/
    └── login.php
```

## Configuration Files

### firebase_credentials.php
This file contains sensitive Firebase credentials and is gitignored. Create it with:

```php
<?php
return [
    'project_id' => 'your-project-id',
    'client_email' => 'firebase-adminsdk-xxxxx@your-project.iam.gserviceaccount.com',
    'private_key' => "-----BEGIN PRIVATE KEY-----\nYOUR_PRIVATE_KEY\n-----END PRIVATE KEY-----\n",
];
```

### app.php
Contains application configuration like JWT settings and timezone.

## Security Notes

- Firebase credentials are stored in a separate file that's gitignored
- JWT tokens are used for API authentication
- Passwords are hashed using PHP's `password_hash()`
- CORS is enabled for cross-origin requests

## Development

### Adding New Features

1. Update API routes in `backend/app/routes/api.php`
2. Create controllers in `backend/app/controllers/`
3. Add business logic in services
4. Update frontend as needed

### Database Schema

Users collection in Firestore:
- `id`: Unique identifier
- `usuario`: Username
- `nombre`: First name
- `apellidoPaterno`: Father's last name
- `apellidoMaterno`: Mother's last name
- `password`: Hashed password
- `direccion`: Address
- `telefono`: Phone
- `ciudad`: City
- `estado`: State
- `activo`: Active status (boolean)
- `deleted`: Soft delete flag (boolean)

## Troubleshooting

### 404 Errors
- Ensure Apache document root is set to the project directory
- Check `.htaccess` configuration
- Verify `mod_rewrite` is enabled

### Firebase Connection Issues
- Verify `firebase_credentials.php` contains correct credentials
- Check Firebase project settings
- Ensure Firestore is enabled

### Login Issues
- Run the admin creation script
- Check PHP error logs
- Verify JWT configuration in `app.php`

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
