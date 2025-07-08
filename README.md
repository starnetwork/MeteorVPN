[![License: AGPL-3.0](https://img.shields.io/badge/License-AGPL%20v3-blue.svg)](https://www.gnu.org/licenses/agpl-3.0.en.html)

# MeteorVPN WHMCS Provisioning Module

MeteorVPN is a WHMCS server provisioning module that integrates with [Defguard](https://defguard.net) via its public REST API. It automates account creation, suspension, unsuspension, termination, and password management, and adds a seamless client-area enrollment workflow for desktop clients.

This module serves as an integration for [Defguard](https://defguard.net) — an open-source identity and access management platform. You can find the Defguard project on [GitHub](https://github.com/Defguard/defguard).

---

## 📋 Table of Contents

- [Features](#-features)  
- [Requirements](#-requirements)  
- [Installation](#-installation)  
- [Configuration](#-configuration)  
- [Usage](#-usage)  
  - [Product Setup](#product-setup)  
  - [Client Area](#client-area)  
  - [Admin Area](#admin-area)  
- [Templates & Localization](#-templates--localization)  
- [Troubleshooting](#-troubleshooting)  
- [Contributing](#-contributing)  
- [License](#-license)  

---

## 🔥 Features

- **Automated Provisioning**  
  - Create, suspend, unsuspend, and terminate user accounts  
  - Change user passwords on demand  
- **Client Area Integration**  
  - One-click desktop enrollment flow  
  - Display account status (Active, Enrolled)  
- **Admin Area Integration**  
  - View user device networks and metadata  
- **Custom Server Groups**  
  - Map WHMCS custom fields (e.g. `group-US-West`, `group-EU-Central`) to API provisioning groups  
- **Multi-language Support**  
  - English & Hebrew translations  
- **Debug & TLS Options**  
  - Enable/disable debug logging  
  - Toggle TLS certificate verification  

---

## 🛠 Requirements

- WHMCS v7.0 or later  
- PHP 7.2+ with **cURL** & **JSON** extensions  
- A running Defguard instance with REST API access  
- Admin API token with full user-management privileges  

---

## 📦 Installation

1. **Upload Module**  
   Copy the `meteorvpn/` directory into your WHMCS installation’s `modules/servers/` folder.  
2. **Activate in WHMCS**  
   - Log in to WHMCS Admin → System Settings → Products/Services.  
   - Click **Create a New Product** (or edit an existing one).  
   - Set **Module** to **MeteorVPN Provisioning**, then click **Continue**.  

---

## ⚙ Configuration

After creating a product, click **Module Settings** and configure:

| Setting               | Type     | Description                                                      | Example                             |
|-----------------------|----------|------------------------------------------------------------------|-------------------------------------|
| **API Base URL**      | Text     | Full URL to your MeteorVPN API endpoint                          | `https://vpn.mycompany.com`         |
| **API Token**         | Password | API token with admin/user-management privileges                  | —                                   |
| **TLS Verify**        | Yes/No   | Verify SSL certificates on API calls                             | `Yes`                               |
| **Debug**             | Yes/No   | Log full requests & responses to WHMCS module log                | `No`                                |
| **Default Group Name**| Text     | Fallback group if no custom field selected                       | `default`                           |

> **Tip:** To leverage custom provisioning groups, define WHMCS _Product Custom Fields_ named `group-<groupname>` (e.g., `group-us-west`). WHMCS will match these to your API groups automatically.

---

## 🚀 Usage

### Custom Fields

1. Inside your product go to **Custom Fields**
2. Add New Custom Field
   - **Field Name:** Must begin with `group-` followed by group name.
   - **Display Name** can be assigned with `|` after the field name e.g. `group-us-west|US New-York`
   - **Field Type:** Select **Checkbox**
   - **Validation:** Leave empty.
3. Save the changes.

When a client orders, WHMCS will call the MeteorVPN API to provision the account automatically and assign it to the selected group(s).

### Client Area

Clients can visit the product page and see:

- **Active** & **Enrolled** status  
- **Enrollment Link** or **Token** for Desktop Client setup 
- Step‐by‐step guide to install & enroll using the Defguard Desktop Client  

### Admin Area

Within a **Client → Products/Services → `<Product Name>`**, You can run module commands: **Create, Suspend, Unsuspend, Terminate, Change Password**.

The **Data** tab shows raw device data returned by the API for quick troubleshooting.

---

## 📑 Templates & Localization

All front-end templates and language files live under:
modules/servers/meteorvpn/

├── templates/

│──── overview.tpl ← Client overview & enrollment UI

└── lang/

├─── english.php ← English translations

└─── hebrew.php ← Hebrew translations

Feel free to customize styles, markup, or add additional languages by following the existing structure.

---

## 🐞 Troubleshooting

- **Enable Debug Mode** in module settings to log API requests/responses.
- Investigate API logs in **modules/servers/meteorvpn/logs/** and in **Utilities → Logs → Module Log**. 
- Verify the **API Base URL** and **Token** are correct and reachable from your server.  
- Ensure your PHP installation has the cURL and JSON extensions enabled.  

---

## 🤝 Contributing

We welcome your bug reports, feature requests, and pull requests!

1. Fork the repository.  
2. Create a new branch: `git checkout -b feature/awesome-feature`.  
3. Commit your changes & add tests if applicable.  
4. Push to your fork and open a Pull Request.

Please adhere to PSR-12 coding standards and include clear commit messages.

---

## ⚖ License

This project is licensed under the **GNU Affero General Public License v3.0** (AGPL-3.0).  
See the [LICENSE](LICENSE) file for full details.

---
