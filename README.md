# YP Assessment Portal

## Project Title and Description
**YP Assessment Portal** is a web-based examination management system designed for **Yayasan Peneraju Developer Assessment Skills**. It facilitates a secure and strict online assessment environment where:
-   **Lecturers/Admins** can create subjects, classes, and exams with specific time windows.
-   **Students** can take exams within a strict "active" window, with automatic submission enforcement.

Key features include:
-   **Strict Exam Visibility**: Exams are hidden until their start time.
-   **Timezone Support**: Interface displays times in **Malaysia Time (MYT)** while storing data in **UTC**.
-   **Security**: Navigation restrictions preventing students from leaving the exam page.
-   **Role-Based Access**: Distinct dashboards for Lecturers and Students.

## Author of the project
-   **Name**: AMIR SYAFIQ BIN EZRIN
-   **Email**: amirezrin2001@gmail.com
-   **GitHub**: https://github.com/amirsyafiqezrin
-   **LinkedIn**: https://www.linkedin.com/in/amirsyafiqezrin/

## Installation / Requirements / Prerequisites
Ensure you have the following installed on your machine:
-   **PHP** >= 8.2
-   **Composer** (PHP Dependency Manager)
-   **Node.js** & **NPM** (JavaScript Package Manager)
-   **MySQL** (Database)

## Tools Used
-   **Framework**: [Laravel 11](https://laravel.com)
-   **Frontend**: Blade Templates, [Tailwind CSS](https://tailwindcss.com), Alpine.js
-   **Authentication**: Laravel Breeze
-   **Database**: MySQL
-   **Timezone**: Application logic handles UTC storage with `Asia/Kuala_Lumpur` display.

## Instructions to Build (Installation)
Follow these steps to set up the project locally:

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/amirsyafiqezrin/yp_assessment.git
    cd yp_assessment
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install JavaScript Dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    -   Copy the example environment file:
        ```bash
        cp .env.example .env
        ```
    -   Open `.env` and configure your database settings:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=yp_assessment
        DB_USERNAME=root
        DB_PASSWORD=
        ```

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations & Seed Database**
    This will create the tables and populate them with sample data (Lecturer/Student accounts).
    ```bash
    php artisan migrate --seed
    ```

## Instructions to Run
1.  **Start the Local Development Server**
    ```bash
    php artisan serve
    ```
    The application will be available at `http://localhost:8000`.

2.  **Start the Frontend Asset Builder** (in a new terminal)
    ```bash
    npm run dev
    ```

## User Credentials (Seed Data)
If you ran `php artisan migrate --seed`, you can log in with:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Lecturer** | `lecturer@yp.com` | `password` |
| **Student** | `student@yp.com` | `password` |

## Review Notes
1.  Roles – The system will have two main roles: Lecturer and Student.
2.  Authentication – Each user will have credentials for secure login.
3.  Exam Creation – Lecturers will be able to create multiple-choice and open-text
questions for various subjects.
4.  Class Management – Students will be grouped into classes.
5.  Subject Management – Each class will be associated with multiple subjects.
6.  Access Control – Students will only be able to access exams that are assigned
to their class.
7.  Time Limit – Exams will have a time limit (e.g., 15 minutes).
8.  Additional Features – You can include any additional features you consider
important.
