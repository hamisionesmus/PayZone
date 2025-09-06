# Payroll Management System - Frontend

A modern, professional, and fully functional web frontend for the Payroll Management System built with PHP, Bootstrap 5, and integrated with RESTful APIs.

## 🚀 Features

### ✨ Modern UI/UX
- **Bootstrap 5** - Responsive and mobile-first design
- **Material Design for Bootstrap** - Professional UI components
- **Dark/Light Mode** - Theme switching capability
- **Smooth Animations** - CSS transitions and JavaScript animations
- **Professional Dashboard** - KPI cards, charts, and analytics

### 🔐 Authentication & Security
- **JWT Integration** - Secure token-based authentication
- **Session Management** - PHP session handling
- **Protected Routes** - Authentication middleware
- **Secure API Calls** - Bearer token authorization

### 📊 Dashboard & Analytics
- **KPI Cards** - Real-time metrics display
- **Interactive Charts** - ApexCharts integration
- **Recent Activity** - Live updates from API
- **Quick Actions** - Fast access to common tasks

### 👥 Employee Management
- **Full CRUD Operations** - Create, Read, Update, Delete
- **Advanced Filtering** - Search and filter employees
- **Pagination** - Efficient data loading
- **Export/Import** - Data management features
- **Responsive Tables** - Mobile-friendly data display

### 💰 Payroll Management
- **Payroll Processing** - Automated calculations
- **Payslip Generation** - PDF generation ready
- **Salary Management** - Employee compensation tracking
- **Payroll History** - Complete transaction records

### 📅 Leave Management
- **Leave Requests** - Employee self-service
- **Approval Workflow** - Manager approvals
- **Leave Balance** - Tracking and calculations
- **Calendar Integration** - Visual leave planning

### 📈 Reports & Analytics
- **Comprehensive Reports** - Payroll, compliance, trends
- **Data Visualization** - Charts and graphs
- **Export Capabilities** - PDF, Excel formats
- **Custom Date Ranges** - Flexible reporting

### ⚙️ Settings & Profile
- **User Profile** - Personal information management
- **Company Settings** - Organization configuration
- **Security Settings** - Password and authentication
- **Preferences** - Theme and notification settings

## 🛠️ Tech Stack

### Frontend Framework
- **PHP 8.1+** - Server-side rendering
- **Bootstrap 5.3** - CSS framework
- **Material Design for Bootstrap** - UI components
- **JavaScript ES6+** - Client-side interactions

### Libraries & Tools
- **ApexCharts** - Advanced charting library
- **SweetAlert2** - Beautiful modal dialogs
- **Font Awesome 6** - Icon library
- **Google Fonts (Inter)** - Typography
- **jQuery 3.7** - DOM manipulation

### API Integration
- **RESTful APIs** - Backend communication
- **JWT Authentication** - Secure token handling
- **AJAX Requests** - Asynchronous data loading
- **JSON Data Format** - API communication

## 📁 Project Structure

```
frontend/
├── assets/
│   ├── css/
│   │   └── style.css          # Custom styles
│   └── js/
│       └── app.js             # Main JavaScript
├── config/
│   └── config.php             # API configuration
├── includes/
│   ├── header.php             # HTML head & dependencies
│   ├── sidebar.php            # Navigation sidebar
│   ├── navbar.php             # Top navigation bar
│   └── footer.php             # JavaScript & closing tags
├── views/
│   ├── auth/
│   │   ├── login.php          # Login page
│   │   └── register.php       # Registration page
│   ├── dashboard/
│   │   └── index.php          # Main dashboard
│   ├── employees/
│   │   └── index.php          # Employee management
│   ├── payroll/
│   │   └── index.php          # Payroll management
│   ├── leave/
│   │   └── index.php          # Leave management
│   ├── reports/
│   │   └── index.php          # Reports & analytics
│   └── settings/
│       └── index.php          # Settings & profile
├── index.php                  # Entry point
└── README.md                  # This file
```

## 🚀 Getting Started

### Prerequisites
- **PHP 8.1 or higher**
- **Web Server** (Apache/Nginx)
- **Backend API** running on `http://localhost:8000`
- **Internet connection** for CDN resources

### Installation

1. **Clone or Download** the frontend files to your web server

2. **Configure API Connection**
   ```php
   // frontend/config/config.php
   define('API_BASE_URL', 'http://localhost:8000');
   ```

3. **Set Up Web Server**
   - Point document root to `frontend/` directory
   - Ensure PHP is enabled
   - Enable URL rewriting if needed

4. **Access the Application**
   ```
   http://localhost/frontend/
   ```

### Backend Integration

Ensure the backend API is running and accessible:

```bash
# Start backend server
cd backend
php -S localhost:8000 -t public
```

## 🔧 Configuration

### API Configuration
```php
// frontend/config/config.php
define('API_BASE_URL', 'http://localhost:8000');
define('API_VERSION', 'api');
```

### Theme Configuration
```php
define('DEFAULT_THEME', 'light'); // 'light' or 'dark'
define('ALLOW_THEME_SWITCH', true);
```

### Session Configuration
```php
// PHP sessions are automatically configured
// Customize in config.php if needed
```

## 🎨 Customization

### Themes
- **Light Theme**: Default professional look
- **Dark Theme**: Modern dark mode
- **Custom Themes**: Modify CSS variables in `style.css`

### Colors
```css
:root {
    --primary-color: #4f46e5;
    --secondary-color: #64748b;
    --success-color: #10b981;
    --danger-color: #ef4444;
}
```

### Branding
- Update company name in `config.php`
- Replace logo in sidebar component
- Customize colors and fonts

## 📱 Responsive Design

### Breakpoints
- **Mobile**: < 768px
- **Tablet**: 768px - 992px
- **Desktop**: > 992px

### Features
- **Collapsible Sidebar** on mobile
- **Responsive Tables** with horizontal scroll
- **Mobile-Optimized Forms** and modals
- **Touch-Friendly** buttons and interactions

## 🔒 Security Features

### Authentication
- **JWT Token Storage** in localStorage
- **Automatic Token Refresh**
- **Secure Logout** with token cleanup
- **Session Timeout** handling

### API Security
- **Bearer Token** authorization
- **HTTPS Enforcement** (recommended)
- **Input Validation** on client and server
- **XSS Protection** with proper escaping

## 📊 API Integration

### Authentication Endpoints
```javascript
POST /api/auth/login
POST /api/auth/register
POST /api/auth/logout
```

### Employee Endpoints
```javascript
GET    /api/employees
POST   /api/employees
GET    /api/employees/{id}
PUT    /api/employees/{id}
DELETE /api/employees/{id}
```

### Payroll Endpoints
```javascript
POST   /api/payroll/run
GET    /api/payroll/runs
GET    /api/payroll/payslips/{employeeId}
```

## 🐛 Troubleshooting

### Common Issues

**1. API Connection Failed**
- Check if backend is running on port 8000
- Verify API_BASE_URL in config.php
- Check browser console for CORS errors

**2. JavaScript Not Working**
- Ensure all CDN links are accessible
- Check browser developer tools for errors
- Verify file paths in includes

**3. Styling Issues**
- Clear browser cache
- Check if Bootstrap CSS is loading
- Verify custom CSS file paths

**4. Authentication Problems**
- Clear browser localStorage
- Check JWT token validity
- Verify backend authentication endpoints

### Debug Mode
Enable debug mode in browser developer tools:
- **Console**: Check for JavaScript errors
- **Network**: Monitor API requests
- **Application**: Check localStorage/sessionStorage

## 🚀 Deployment

### Production Checklist
- [ ] Update API_BASE_URL to production URL
- [ ] Enable HTTPS
- [ ] Configure proper error handling
- [ ] Set up logging
- [ ] Optimize assets (minify CSS/JS)
- [ ] Configure caching headers

### Server Requirements
- **PHP 8.1+**
- **MySQL 8.0+** (for backend)
- **Apache/Nginx** with PHP-FPM
- **SSL Certificate** (recommended)
- **Composer** (for dependency management)

### Performance Optimization
- **Enable GZIP** compression
- **Set up CDN** for static assets
- **Implement caching** strategies
- **Optimize database queries**
- **Minify CSS and JavaScript**

## 📞 Support

For issues and questions:
1. Check this README
2. Review browser console errors
3. Verify backend API connectivity
4. Check PHP error logs

## 📄 License

This project is part of the Payroll Management System and follows the same licensing terms as the backend.

---

**Built with ❤️ for modern payroll management**