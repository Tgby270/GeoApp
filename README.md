# 🗺️ GeoApp

An IUT academic project — a web application that displays **sports equipment and facilities across France** on an interactive map, using open data from **data.gouv.fr**. Users can explore nearby establishments and make reservations directly through the app.

---

## 📋 Table of Contents

- [Features](#features)
- [Project Structure](#project-structure)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
- [API Reference](#api-reference)
- [Authors](#authors)

---

## ✨ Features

- 🗺️ Interactive map displaying sports facilities and equipment across France
- 📍 Geolocation support to find establishments near you
- 🔍 Search and filter by sport type or location
- 📅 Reservation system to book facilities directly through the app
- 📊 Data sourced live from the official French open data portal (data.gouv.fr)

---

## 📁 Project Structure
```
GeoApp/
├── index.php           # Main entry point
├── HTML/               # HTML page templates
├── PHP/                # Backend logic, API calls, reservation handling
├── CSS/                # Stylesheets
├── map/                # Map rendering and geolocation logic
├── ressource/          # Static assets (images, icons, etc.)
└── .gitignore
```

---

## 🛠️ Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP |
| Frontend | HTML5, CSS3, JavaScript |
| Map | Leaflet.js (or equivalent) |
| Data Source | data.gouv.fr Open API https://www.data.gouv.fr/dataservices/api-data-es|

---

## 🚀 Getting Started

### Prerequisites

- A web server with PHP support (e.g. [XAMPP](https://www.apachefriends.org/), [WAMP](https://www.wampserver.com/), or Apache)
- PHP 7.4+
- A modern web browser

### Installation

1. **Clone the repository**
```bash
   git clone https://github.com/Tgby270/GeoApp.git
   cd GeoApp
```

2. **Move the project to your server's web root**
```bash
   # Example with XAMPP on Windows
   cp -r GeoApp/ C:/xampp/htdocs/GeoApp
```

3. **Start your local server** (e.g. via XAMPP Control Panel or CLI)

4. **Open the app in your browser**
```
   http://localhost/GeoApp/
```

---

## 🌐 API Reference

This project uses the **data.gouv.fr** open data platform to retrieve sports infrastructure data in France.

- **Portal:** [https://www.data.gouv.fr](https://www.data.gouv.fr/dataservices/api-data-es)
- **No API key required** — the data is freely and publicly accessible.

---

## 👥 Authors

Developed by a team of 4 students at IUT.

- **Grossin Tristan** — [GitHub](https://github.com/Tgby270)
- **Coombes Ethan** — [GitHub](https://github.com/EthanCoombes)
- **Marchand Valentin** — [GitHub](https://github.com/marchandvalentin)
- **Silva LéO** — [GitHub](https://github.com/avlis936)


---

## 📄 License

This project was created for educational purposes. No license is currently specified.
