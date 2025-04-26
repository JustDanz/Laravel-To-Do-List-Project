# Laravel To-Do List

A simple **To-Do List** application built using **Laravel**, one of the most powerful PHP frameworks. This app allows users to easily add, edit, mark as completed, and delete tasks with a clean and responsive interface.

## Features
- Add new tasks
- Mark tasks as completed or pending
- Edit task names
- Delete tasks
- Input validation
- User-friendly UI with Bootstrap 5 or Tailwind CSS (optional)
- Manage data with Eloquent ORM

## Technologies Used
- Laravel (Version 10.x or latest)
- PHP 8.x
- MySQL / SQLite
- Bootstrap 5 / Tailwind CSS (optional)

## Installation

Follow these steps to set up the project locally:

1. Clone the repository
   ```bash
   git clone https://github.com/your-username/laravel-todolist.git
   ```

2. Navigate into the project directory
   ```bash
   cd laravel-todolist
   ```

3. Install PHP and Node.js dependencies
   ```bash
   composer install
   npm install && npm run dev
   ```

4. Copy the `.env` file and generate the application key
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. Set up your database in the `.env` file, then run the migrations
   ```bash
   php artisan migrate
   ```

6. Start the development server
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`.

## Contribution

Contributions are welcome!  
If you want to contribute, please fork the repository, create a new branch, and submit a pull request.  
For major changes, please open an issue first to discuss what you would like to change.

---

**Happy coding! 🚀**
```
