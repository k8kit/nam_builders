# NAM Builders and Supply Corp - Promotional Website

A professional, full-stack promotional website for NAM Builders and Supply Corp built with HTML, CSS, Bootstrap, JavaScript, PHP, and MySQL.

## Features

✅ **Responsive Frontend**
- Modern hero section with call-to-action buttons
- About section highlighting company values
- Services gallery with images from database
- Clients carousel (auto-scrolling right-to-left continuously)
- Contact form with email submission
- Professional footer

✅ **Admin Panel**
- Secure login system
- Dashboard with statistics
- CRUD operations for Clients
- CRUD operations for Services
- Contact message management
- Image upload functionality

✅ **Database Integration**
- MySQL database with optimized schema
- Support for image storage and management
- Contact message tracking

## Project Structure

```
nam-builders/
├── index.php                    # Main website
├── config/
│   └── database.php            # Database configuration & connection
├── includes/
│   └── functions.php           # Utility functions
├── admin/
│   ├── login.php               # Admin login page
│   ├── dashboard.php           # Admin dashboard
│   └── pages/
│       ├── overview.php        # Dashboard overview
│       ├── clients.php         # Client management
│       ├── services.php        # Service management
│       └── messages.php        # Contact messages
├── backend/
│   ├── save_client.php         # Save/Update client
│   ├── get_client.php          # Get client details
│   ├── delete_client.php       # Delete client
│   ├── save_service.php        # Save/Update service
│   ├── get_service.php         # Get service details
│   ├── delete_service.php      # Delete service
│   ├── submit_contact.php      # Process contact form
│   ├── get_message.php         # Get message details
│   ├── delete_message.php      # Delete message
│   └── logout.php              # Logout handler
├── css/
│   └── style.css               # Main stylesheet
├── js/
│   ├── carousel.js             # Carousel animation
│   └── admin.js                # Admin panel utilities
├── uploads/
│   ├── clients/                # Client logo storage
│   └── services/               # Service image storage
├── database/
│   └── nam_builders.sql        # Database schema
└── README.md                    # This file
```

## Installation & Setup

### Prerequisites
- XAMPP (or similar PHP/MySQL local server)
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Modern web browser

### Step 1: Install XAMPP

1. Download XAMPP from https://www.apachefriends.org/
2. Install and start XAMPP
3. Ensure Apache and MySQL are running

### Step 2: Clone/Download Project

1. Download the project files
2. Extract to XAMPP's `htdocs` folder:
   - Windows: `C:\xampp\htdocs\nam-builders`
   - Mac: `/Applications/XAMPP/htdocs/nam-builders`
   - Linux: `/opt/lampp/htdocs/nam-builders`

### Step 3: Create Database

1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "New" to create a new database
3. Database name: `nam_builders`
4. Click "Create"
5. Select the new `nam_builders` database
6. Click "Import"
7. Choose the `database/nam_builders.sql` file
8. Click "Go" to import

**Or via MySQL Command Line:**
```bash
mysql -u root -p < database/nam_builders.sql
```

### Step 4: Configure Database Connection

Edit `config/database.php` if needed:
```php
define('DB_HOST', 'localhost');      // Database host
define('DB_USER', 'root');           // MySQL username
define('DB_PASS', '');               // MySQL password (blank for XAMPP default)
define('DB_NAME', 'nam_builders');   // Database name
define('DB_PORT', 3306);             // MySQL port
```

### Step 5: Create Upload Directories

The upload directories will be created automatically, but ensure `htdocs` has write permissions.

**On Windows:**
- Right-click `nam-builders` folder → Properties → Security → Edit → Allow Full Control

**On Mac/Linux:**
```bash
chmod -R 755 /path/to/nam-builders
chmod -R 777 /path/to/nam-builders/uploads
```

### Step 6: Access the Website

1. **Frontend:** http://localhost/nam-builders/
2. **Admin Panel:** http://localhost/nam-builders/admin/login.php
3. **Default Admin Credentials:**
   - Username: `admin`
   - Password: `admin123`

> ⚠️ **Important:** Change the default admin password immediately after first login!

## Usage Guide

### Frontend Website

**Home Page Features:**
- Navigate through sections using the top menu or buttons
- View services and client logos
- Submit contact form inquiries
- Clients carousel auto-scrolls continuously (pause on hover)

### Admin Panel

**Login:**
1. Go to Admin Panel link
2. Enter credentials (default: admin/admin123)
3. Click Login

**Dashboard Overview:**
- View statistics (total clients, services, messages)
- Quick access buttons to manage content

**Managing Clients:**
1. Click "Clients" in sidebar
2. Click "Add New Client" or edit existing
3. Upload logo/image, fill details
4. Set display order and active status
5. Click "Save Client"

**Managing Services:**
1. Click "Services" in sidebar
2. Click "Add New Service" or edit existing
3. Upload service image, fill details
4. Arrange display order
5. Click "Save Service"

**Viewing Messages:**
1. Click "Messages" in sidebar
2. Click eye icon to view message details
3. Click trash icon to delete
4. Unread messages show notification badge

## Database Schema

### admin_users
```sql
- id (Primary Key)
- username (Unique)
- password (Hashed)
- email
- created_at
- updated_at
```

### clients
```sql
- id (Primary Key)
- client_name
- image_path (Reference to uploads folder)
- description
- is_active
- sort_order
- created_at
- updated_at
```

### services
```sql
- id (Primary Key)
- service_name
- description
- image_path
- icon_class
- is_active
- sort_order
- created_at
- updated_at
```

### contact_messages
```sql
- id (Primary Key)
- full_name
- email
- phone
- service_needed
- message
- is_read
- created_at
```

## File Upload Information

**Supported Formats:**
- JPG, JPEG, PNG, GIF, WebP

**Maximum File Size:**
- 5 MB per file

**Upload Locations:**
- Client logos: `/uploads/clients/`
- Service images: `/uploads/services/`

## Security Features

✅ **Password Security**
- Passwords hashed with bcrypt (cost: 10)
- Secure password verification

✅ **Input Validation**
- All user inputs sanitized
- SQL injection protection via prepared statements
- File upload validation

✅ **Session Management**
- Session timeout after 1 hour of inactivity
- Secure session storage

✅ **Admin Protection**
- Login required for admin panel
- Automatic redirect for unauthorized access

## Customization

### Update Company Information
Edit in `index.php`:
```php
// Hero section text
// Footer contact information
// About section content
```

### Change Colors
Edit `css/style.css` - CSS variables:
```css
:root {
    --primary-color: #FF5722;
    --secondary-color: #2C3E50;
    --light-bg: #F8F9FA;
    /* ... more colors ... */
}
```

### Modify Services List
Add/remove services via Admin Panel

### Change Admin Password
Edit `config/database.php` and re-insert admin user with new hashed password:
```php
// Hash a new password
echo password_hash('new_password', PASSWORD_BCRYPT, ['cost' => 10]);
```

## Troubleshooting

### 1. **Database Connection Error**
- Check MySQL is running in XAMPP
- Verify credentials in `config/database.php`
- Ensure database `nam_builders` exists

### 2. **Upload Directory Error**
- Check folder permissions: `chmod 777 uploads/`
- Ensure `/uploads/clients/` and `/uploads/services/` exist

### 3. **Admin Login Not Working**
- Clear browser cookies/cache
- Check database for admin_users table
- Verify password is correctly hashed

### 4. **Images Not Showing**
- Check image file paths in database
- Verify images exist in upload folders
- Check image file permissions

### 5. **Admin Panel Not Loading**
- Check session configuration in `config/database.php`
- Ensure login.php redirects properly
- Check browser error console

## Performance Optimization

- Images are dynamically loaded from database
- Database queries use prepared statements
- Carousel animation is CSS-based (hardware accelerated)
- Bootstrap CDN for fast CSS/JS loading

## Browser Compatibility

✅ Chrome 90+
✅ Firefox 88+
✅ Safari 14+
✅ Edge 90+
✅ Mobile browsers

## Maintenance

### Regular Tasks
1. Monitor contact messages in admin panel
2. Update client and service information
3. Check disk space for uploads
4. Backup database regularly

### Database Backup
```bash
mysqldump -u root nam_builders > backup.sql
```

### Database Restore
```bash
mysql -u root nam_builders < backup.sql
```

## License

This website is created for NAM Builders and Supply Corp.

## Support & Contact

For issues or questions, contact the development team.

---

**Version:** 1.0
**Last Updated:** 2024
