# KETIKET

  Proyek ini adalah platform e-commerce berbasis web yang dibangun dengan Laravel 12, yang dirancang khusus untuk memfasilitasi pembelian tiket perjalanan secara online. Dengan fokus pada pengalaman pengguna yang intuitif dan efisien, platform ini memungkinkan pelanggan untuk mencari, memilih, dan membeli tiket perjalanan dari berbagai penyedia layanan.

## Requirements

Before you begin, make sure you have met the following requirements:

- **PHP** >= 8.2 or better ([php](https://www.php.net/downloads.php))
- **Composer** ([composer](https://getcomposer.org/download/))
- **Node.js** >= 12.x ([nodejs](https://nodejs.org/en/download/))
- **NPM** >= 6.x 
- **Git** ([git](https://git-scm.com/downloads))

And if you dont have those... Yeah you should install it.

I'm not gonna Yap alot. And if you confused about installing, YouTube do exist to teach you.

## Installation

1. **First of all, Clone the repository:**
    ```bash
    git clone https://github.com/ntesseract/ketiket.git
    cd ketiket
    ```

2. **Install dependencies:**
    ```bash
    composer install
    npm install
    ```

<!-- 3. **Build vite dependencies:**
    ```bash
    npm run build
    ``` -->

3. **Copy the `.env` file and generate an application key:**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4. **If it was neccessary, go set up your database configuration in the `.env` file.**

5. **Run the database migrations:**
    ```bash
    php artisan migrate
    ```

6. **Run or Start the development server:**
    ```bash
    composer run dev
    ```

## Configuration

Make sure to configure your environment variables in the `.env` file. Key settings include:

- `DB_CONNECTION`
- `DB_HOST`
- `DB_PORT`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`

## Usage

To use the application, navigate to `http://localhost:8000` in your web browser. You can now start interacting with the ketiket application.
