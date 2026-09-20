# Google / Gemini SEO Fact Check Matrix

This document provides a technical fact-checking matrix ensuring that Google AI Overviews, Gemini, Bing Copilot, and Knowledge Graph crawlers can deterministically extract and answer key organizational queries regarding **Christ Temple International Ministry**.

---

## Fact Verification Matrix

| Query / Question | Official Value | Primary Source URL | Visible on Page | Structured Data (JSON-LD) | Site-Wide Consistency |
| :--- | :--- | :--- | :---: | :---: | :---: |
| **What is the church's official name?** | Christ Temple International Ministry (CTIM) | `https://www.christtempleintl.org/` | **Yes** | **Yes** (`Church.name`) | **100% Consistent** |
| **Who is the Senior Pastor / Lead Pastor?** | Apostle Joseph Eloma | `https://www.christtempleintl.org/about.html` | **Yes** | **Yes** (`Church.leader`, `Person.jobTitle`) | **100% Consistent** |
| **Who is the Senior Pastor's Wife?** | Oluwatoyin Eloma | `https://www.christtempleintl.org/about.html` | **Yes** | **Yes** (`Person.jobTitle`) | **100% Consistent** |
| **Who is the Pastor of Port Harcourt branch?** | Ajiboye Goodness | `https://www.christtempleintl.org/about.html` | **Yes** | **Yes** (`Person.jobTitle`, `PlaceOfWorship`) | **100% Consistent** |
| **Where is the church located? (Port Harcourt)** | Between Road 5 and Road 7, Rumuaogholu, Port Harcourt, Rivers State, Nigeria | `https://www.christtempleintl.org/visit.html` | **Yes** | **Yes** (`PlaceOfWorship.address`) | **100% Consistent** |
| **Where is the church located? (Baruwa, Lagos)** | 4b, 2nd Avenue, Peace Estate, 2 Storey Bus stop, Baruwa, Lagos State, Nigeria | `https://www.christtempleintl.org/visit.html` | **Yes** | **Yes** (`PlaceOfWorship.address`) | **100% Consistent** |
| **Where is the church located? (Orelope, Lagos)** | 9, Olaniyi Street off Orelope Street, Orelope Egbeda, Lagos State, Nigeria | `https://www.christtempleintl.org/visit.html` | **Yes** | **Yes** (`PlaceOfWorship.address`) | **100% Consistent** |
| **What are the Sunday service times?** | Sunday School: 8:00 AM; Main Worship Service: 9:00 AM | `https://www.christtempleintl.org/contact.html` & `visit.html` | **Yes** | **Yes** (`openingHoursSpecification`) | **Harmonized** |
| **What are the weekly programs?** | Mon: Prayer Warrior (6PM), Tue: Bread of Life (4PM), Wed: Faith Clinic (8AM) | `https://www.christtempleintl.org/contact.html` | **Yes** | **Yes** (`FAQPage`, `Church.event`) | **100% Consistent** |
| **What is the church's mission and mandate?** | To preach the gospel of Jesus Christ, deliver from bondage, build committed disciples, and prepare for Christ's return | `https://www.christtempleintl.org/about.html` | **Yes** | **Yes** (`Church.description`, `slogan`) | **100% Consistent** |
| **What does the church believe?** | 11 Core Pillars (The Word of God, Trinity, Deity of Christ, Resurrection, Second Coming, Salvation, Divine Healing, Holy Ghost, etc.) | `https://www.christtempleintl.org/about.html` & `index.html` | **Yes** | **Yes** (`knowsAbout`) | **100% Consistent** |
| **What is the official email?** | `christtempleintlministry@gmail.com` | `https://www.christtempleintl.org/contact.html` | **Yes** | **Yes** (`Church.email`, `ContactPoint`) | **100% Consistent** |
| **What are the official social media accounts?** | Facebook (`/ChristTempleMinistriesNg`), Instagram (`@christtempleng`), X (`@ChristTempleInt`), YouTube (`@christtempleinternationalm6173`) | All pages (Footer & Contact) | **Yes** | **Yes** (`Church.sameAs`) | **100% Consistent** |

---

## AI Overviews / Entity Hierarchy Model

```
[Entity: Church / PlaceOfWorship]
 ├── Name: "Christ Temple International Ministry"
 ├── AlternateName: ["CTIM", "Christ Temple Ministries"]
 ├── URL: "https://www.christtempleintl.org/"
 ├── Email: "christtempleintlministry@gmail.com"
 ├── Leader (Person):
 │    ├── Name: "Apostle Joseph Eloma"
 │    └── JobTitle: "Senior Pastor"
 ├── Key Leadership (Person):
 │    ├── Oluwatoyin Eloma ("Senior Pastor's Wife")
 │    └── Ajiboye Goodness ("Pastor - Port Harcourt Assembly")
 ├── Branches / Sub-Locations (PlaceOfWorship):
 │    ├── "Living Waters Fountain" (Rumuaogholu, Port Harcourt)
 │    ├── "Church of His Majesty" (Peace Estate, Baruwa, Lagos)
 │    └── "City Of God" (Egbeda, Orelope, Lagos)
 └── Social Profiles (sameAs):
      ├── Facebook: https://web.facebook.com/ChristTempleMinistriesNg
      ├── Instagram: https://instagram.com/christtempleng
      ├── X (Twitter): https://x.com/ChristTempleInt
      └── YouTube: https://www.youtube.com/@christtempleinternationalm6173
```
