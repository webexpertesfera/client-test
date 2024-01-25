# Web scanner

**This software scans a domain and fetches all the links from it and checks whether the links are correct urls or 404 ones. Stack used is**

1. Backend - laravel (9)
2. Frontend - Vue (2)
3. Database - Mysql

**To setup backend do the following steps**

1. Go to backend folder and run the command "composer install"
2. Create env file and enter correct database credentials
3. Run this command "php artisan migrate:fresh"

**To setup frontend**

1. Go to frontend folder and run the command "npm install"
2. Then start the server by using this command "npm run serve"

Note: If you have deployed this project on any server then you can change backend url in LinkScanner.vue file and deploy both frontend and backend on same server to not face the cors issue.
