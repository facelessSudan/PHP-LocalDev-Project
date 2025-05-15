#  Local Resume AI Agent

A fully local, privacy-preserving Resume Screening AI Agent built with PHP, SQLite, and n8n. This project mimics cloud functionality entirely offline — no API keys, no subscriptions, and full control over your data and workflows.

---

##  Features

- Resume upload form with local PDF storage
- Rule-based or local LLM (Ollama) resume scoring
- Local automation workflow via [n8n](https://n8n.io/)
- Email notifications through [MailHog](https://github.com/mailhog/MailHog)
- SQLite-powered applicant and job application database
- Completely offline — no external services required
- Educational test suite to validate local functionality

---

##  Tools & Technologies Used

| Tool/Service     | Purpose                          |
|------------------|----------------------------------|
| **PHP 8.x**       | Core application logic           |
| **Composer**      | Dependency management            |
| **n8n (local)**   | Automation workflow engine       |
| **SQLite**        | Local applicant database         |
| **MailHog**       | Email testing tool (SMTP + UI)   |
| **PHPMailer**     | Email sending from PHP           |
| **Ollama** *(opt)*| Local AI LLM (e.g. LLaMA2)       |
| **Node.js**       | Required for n8n                 |

---

##   Project Structure
```PHP-Directory/
├── composer.json
├── .env
├── public/
│ └── index.php
├── src/
│ ├── AIrecruitementAgent.php
│ ├── webhook_handler.php
│ ├── form_handler.php
│ ├── views/
│ │ └── application_form.php
│ └── Services/
│ ├── DatabaseService.php
│ ├── LocalAIService.php
│ ├── LocalMailService.php
│ └── FileStorageService.php
├── config/
│ └── database.php
├── database/
│ └── recruitment.db
├── uploads/
├── logs/
├── tests/
│ └── test_local_workflow.php
└── README.md
```
---

##   Prerequisites

- PHP 8.x+
- Composer
- Node.js 16+ (for n8n)
- MailHog
- SQLite
- Optional: [Ollama](https://ollama.com/) for running local LLMs

---

##   Setup Instructions

### 1. Clone & Install

```bash
git clone https://github.com/PHP-LocalDev-Project/local-resume-ai.git
cd local-resume-ai
composer install
```

### 2. Start Local Services
You can decide to open the terminals as stated below or run ```./start``` file as it automates the startup of local services. 
```bash
# Terminal 1 - PHP Web Server
php -S localhost:8000 -t public

# Terminal 2 - Start n8n
n8n start

# Terminal 3 - Start MailHog
mailhog

# Terminal 4 - Ollama (optional for LLM)
ollama serve
```

### 3. Initialize Database

```bash
sqlite3 database/recruitment.db < config/schema.sql
```

### 4. Configure Environment
Create a .env file: (Am not hiding it to enable running of config environment)
```bash
APP_ENV=local
APP_URL=http://localhost:8000
APP_DEBUG=true

N8N_WEBHOOK_URL=http://localhost:5678/webhook/resume-processing

MAIL_HOST=localhost
MAIL_PORT=1025
MAIL_FROM_ADDRESS=hr@localcompany.test
MAIL_FROM_NAME="Local HR Department"

DB_CONNECTION=sqlite
DB_DATABASE=database/recruitment.db

AI_SERVICE=local
# Optional for LLM:
# OLLAMA_API_URL=http://localhost:11434
```

### 5. Configure n8n Workflow
- Open http://localhost:5678
- Import the provided JSON workflow
- Update the webhook URL in your .env.local

## Test Suite
To validate that everything is running correctly:
```bash
php tests/test_local_workflow.php
```
This script will:

- Insert dummy data into the database
- Test AI scoring
- Simulate an email send via MailHog

## For Demos (and probably presentation)

- Upload a resume via http://localhost:8000
- Show the workflow execution in n8n (localhost:5678)
- Check emails in MailHog (localhost:8025)
- Browse database with any SQLite viewer
- Explain architecture and AI scoring logic

## Benefits of Local Setup
- Privacy: All data stays on your machine
- Zero Cost: No subscriptions or paid APIs
- Modular: Swap or extend components easily
- Educational: Learn webhooks, LLMs, and automation
- Fast: No external network latency
- Testable: Includes local test suite

## License
This project is open-source and available under the MIT License.

## Author
Robert Okoba
   
