BlueCollar E-Commerce Platform

BlueCollar is a digital ecosystem designed to modernize the procurement of tools and materials for blue-collar workers in Ghana and West Africa. By connecting competent artisans (buyers) with verified suppliers, the platform eliminates supply chain inefficiencies and ensures access to genuine, high-quality equipment.

More Info on YouTube: https://www.youtube.com/watch?v=0i6_5okV3YQ

📖 About the Project

The blue-collar sector in West Africa is largely unorganized, leading to fragmented supply chains, pricing opacity, and a proliferation of counterfeit materials. BlueCollar addresses these issues by providing:

For Artisans: A mobile-first marketplace to discover, compare, and purchase authentic tools.

For Suppliers: A dedicated portal (BlueCollar.supply) to manage inventory, reach a structured customer base, and gain market insights.

🚀 Key Features

Role-Based Access Control: Distinct dashboards for Buyers (Mechanics, Electricians, etc.), Suppliers, and Admins.

Digital Storefronts: Suppliers can customize their profiles to build brand identity.

Smart Invoicing System: Automated sequential invoice generation (i00n + 1) and real-time total calculation via database triggers.

Integrated Payments: Support for local payment methods including MTN Mobile Money, Vodafone Cash, and Card payments via Paystack.

Trust & Safety: Verified supplier badges, product warranties, and community ratings/reviews.

🛠️ Tech Stack

Backend: PHP (MVC Architecture)

Database: MySQL / MariaDB (InnoDB Engine)

Frontend: HTML5, CSS3, JavaScript (Mobile-First Design)

Server: Apache / Nginx

⚙️ Installation & Setup

Prerequisites

PHP >= 8.0

MySQL >= 5.7 or MariaDB >= 10.4

Apache/Nginx Web Server

Steps

Clone the Repository

git clone [https://github.com/yourusername/bluecollar.git](https://github.com/yourusername/bluecollar.git)
cd bluecollar


Database Configuration

Create a new MySQL database named BlueCollar.

Import the provided SQL schema file located in /database/BlueCollar.sql.

Note: This script handles table creation (using the final_ prefix) and sets up necessary triggers.

Application Config

Rename config.example.php to config.php (if applicable).

Update your database credentials:

define('DB_HOST', 'localhost');
define('DB_NAME', 'BlueCollar');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');


Run the Application

Serve the application via your local web server (e.g., XAMPP, WAMP, or PHP built-in server):

php -S localhost:8000


🗄️ Database Structure

The project uses a relational database schema. Key tables include:

final_users: Central authentication and role management.

final_products: Inventory items linked to categories and brands.

final_orders & final_invoices: Transactional records.

final_seller_storefront: Supplier profile data.

🤝 Contributing

Contributions are welcome! Please fork the repository and create a pull request for any feature updates or bug fixes.

📄 License

This project is licensed under the MIT License - see the LICENSE file for details.
