## 環境構築

### Dockerビルド
- `git clone https://github.com/kousukekunitomo/flea-market-app`
- `cd flea-market-app`
- `docker compose up -d --build`

### Laravel環境構築
- `docker compose exec app bash`
- `composer install`
- `cp .env.example .env`
- `php artisan key:generate`
- `php artisan migrate`
- `php artisan db:seed`
- `php artisan storage:link`


### Laravel環境構築
- `新規登録: http://localhost:8000/register`
- `ログイン: http://localhost:8000/login`
- `MailHog: http://localhost:8025`
- `phpMyAdmin: http://localhost:8080`

## ER Diagram

<p align="center">
  <img src="public/images/er-diagram-flea-2025-10.png" alt="ER Diagram of flea-market-app" width="720">
</p>
