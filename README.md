# Pharmacy & Medicine POS & Inventory Management System

A production-ready **Pharmacy, Medicine POS, and Inventory Management System** built with **Laravel 12, Livewire, Tailwind CSS, and SQLite/MySQL**.

---

## 🌟 Key Features

- **Medicine Unit & Packaging Hierarchy**: Arbitrary multi-level packaging (e.g. `Box > Pack > Strip > Tablet`).
- **Base Unit Stock Storage (Single Source of Truth)**: All inventory is strictly stored in Base Units, with automatic cascading conversions.
- **Human-Readable Stock Breakdown**: Automatic display of inventory in hierarchical units (e.g., `8 Packs, 5 Strips, 0 Tablets (Total: 850 Tablets)`).
- **Fast POS Counter**: Barcode scanner lookup per packaging level, dynamic unit dropdown, FEFO batch allocation, quick customer creation, and receipt printing.
- **Purchase Invoicing & Supplier Management**: Multi-unit purchasing, automatic batch creation, and supplier ledger tracking.
- **Sales & Purchase Returns**: Historical frozen conversion factor guarantees transaction immutability.
- **Reports & Analytics**: Sales, Profit/Loss, Best Selling, Stock, Expiry, and Supplier Payable reports.

---

## 🚀 Quick Start (Local Setup)

1. **Clone repository**:
   ```bash
   git clone https://github.com/faizanlodhi035-sys/pharmacy-system.git
   cd pharmacy-system
   ```

2. **Install PHP Dependencies**:
   ```bash
   composer install
   ```

3. **Configure Environment & Database**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   touch database/database.sqlite
   php artisan migrate --seed
   ```

4. **Run Development Server**:
   ```bash
   php artisan serve
   ```
   Open `http://localhost:8000` in your browser.

---

## 🌐 1-Click Free Cloud Deployment (Render / Koyeb / Railway)

This repository includes pre-configured **`Dockerfile`**, **`render.yaml`**, **`Procfile`**, and **`nixpacks.toml`** for zero-configuration 1-click cloud deployment.

---

## 📄 License
Open-sourced software under the [MIT license](LICENSE).
