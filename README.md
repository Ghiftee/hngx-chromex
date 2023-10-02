# CHROME EXTENSION BACKEND

> ## About this project

This project is the backend for the chrome extension app which stores recorded videos, and renders pages with the videos as needed. This documnet contains all endpoints created and their usuage.

> ## Installation
1. Clone the repository.
2. Install dependencies with `composer install`.
3. Set up your database configuration in `.env`.
4. Run migrations with `php artisan migrate`.
5. Start the development server with `php artisan serve`.

> ## API Endpoints

- view all videos in db(METHOD: GET): `https://chrome-extension-3rhg.onrender.com/api/`
- submit a recorded video(METHOD: POST): `https://chrome-extension-3rhg.onrender.com/api/submit`
- display a specific video by id(METHOD: GET): `https://chrome-extension-3rhg.onrender.com/api/getVideo/{id}`
- search for videos by name or id(METHOD: GET): `https://chrome-extension-3rhg.onrender.com/api/search/{nameOrId}`


> ## LICENSE
This project is [MIT licensed](./LICENSE)











