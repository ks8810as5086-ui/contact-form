# COACHTECH お問い合わせフォーム

## 概要
本プロジェクトは、ユーザーが問い合わせ内容を送信し、管理者がその内容を閲覧・管理できる「お問い合わせフォームシステム」です。
フロントエンドのフォーム機能に加え、管理者用の管理画面（認証機能付き）、および外部システムとの連携を想定した公開API機能を実装しています。
ご提示いただいた詳細な手順を、GitHub等のリポジトリでそのまま使える形に整理し、`README.md`（環境構築セクション）としてまとめました。

---

# 開発環境構築ガイド

本プロジェクトは Docker (Laravel Sail) を使用して開発環境を構築します。以下の手順に従って環境をセットアップしてください。
1. プロジェクトの作成と初期設定
まず、Laravelプロジェクトを作成し、Sailをインストールします。

# Laravelプロジェクトの作成 (Laravel 10.x)
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer create-project laravel/laravel:^10.0 contact-form-app

cd contact-form-app

# Laravel Sailをインストール
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    composer require laravel/sail --dev

# Sail設定のパブリッシュ（MySQLを指定）
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    -e COMPOSER_CACHE_DIR=/tmp/composer_cache \
    laravelsail/php82-composer:latest \
    php artisan sail:install --with=mysql
> **注意**: `docker-compose.yml` 内の `mysql` 設定に `platform: 'linux/amd64'` が含まれていることを確認してください。

2. 環境変数 (.env) の設定
`.env` ファイルを開き、データベース接続情報を確認・修正してください。
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=sail
DB_PASSWORD=password

3. フロントエンドのセットアップ
Tailwind CSSおよび必要な依存関係をインストールします。
# 依存パッケージのインストール
./vendor/bin/sail npm install
./vendor/bin/sail npm install -D tailwindcss@^3.4.0 postcss autoprefixer
./vendor/bin/sail npm install alpinejs

# Tailwindの設定ファイル生成
./vendor/bin/sail npx tailwindcss init -p

`tailwind.config.js` を開き、`content` セクションを以下のように更新してください。
content: [
  "./resources/**/*.blade.php",
  "./resources/**/*.js",
  "./resources/**/*.vue",
],

4. サービスの追加 (phpMyAdmin)
`docker-compose.yml` に `phpmyadmin` を追記します。
phpmyadmin:
    image: 'phpmyadmin:latest'
    ports:
        - '${FORWARD_PHPMYADMIN_PORT:-8080}:80'
    environment:
        PMA_HOST: mysql
        PMA_USER: '${DB_USERNAME}'
        PMA_PASSWORD: '${DB_PASSWORD}'
    networks:
        - sail
    depends_on:
        - mysql

5. アプリケーションの起動と初期化
Sailの起動
./vendor/bin/sail up -d

エイリアスの設定 (zshの場合)
echo "alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'" >> ~/.zshrc
source ~/.zshrc

アプリケーションキーの生成
sail artisan key:generate

データベースのマイグレーションとシーディング
sail artisan migrate --seed

Vite開発サーバーの起動 (別ターミナルで実行)
sail npm run dev

以上で環境構築は完了です。ブラウザから `http://localhost` にアクセスして動作を確認してください。phpMyAdminは `http://localhost:8080` からアクセス可能です。

## 技術スタック
| カテゴリ | 技術・ツール |
| :--- | :--- |
| **OS** | Dockerが動作する環境 |
| **言語・フレームワーク** | PHP 8.2, Laravel 10.x |
| **データベース** | MySQL 8.0 |
| **Webサーバー** | Nginx |
| **フロントエンド** | Vite, Tailwind CSS ^3.4.0 |
| **開発環境・ツール** | Docker, Laravel Sail, phpMyAdmin |

## APIエンドポイント一覧

| メソッド | パス | 概要 |
| :--- | :--- | :--- |
| `GET` | `/api/v1/contacts` | お問い合わせ一覧取得（検索・ページネーション対応） |
| `GET` | `/api/v1/contacts/{id}` | お問い合わせ詳細取得 |
| `POST` | `/api/v1/contacts` | お問い合わせ新規作成 |
| `PUT` | `/api/v1/contacts/{id}` | お問い合わせ更新 |
| `DELETE` | `/api/v1/contacts/{id}` | お問い合わせ削除 |

## 開発環境構築手順

### 1. プロジェクトの作成とSailインストール
```bash
# プロジェクト作成
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html -e COMPOSER_CACHE_DIR=/tmp/composer_cache laravelsail/php82-composer:latest composer create-project laravel/laravel:^10.0 contact-form-app

cd contact-form-app

# Sailインストールとセットアップ
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html -e COMPOSER_CACHE_DIR=/tmp/composer_cache laravelsail/php82-composer:latest composer require laravel/sail --dev
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html -e COMPOSER_CACHE_DIR=/tmp/composer_cache laravelsail/php82-composer:latest php artisan sail:install --with=mysql
### 主な機能
* **お問い合わせフォーム**: ユーザーによる問い合わせ内容の送信および確認機能。
* **管理者認証**: ユーザー登録・ログインによるセキュアな管理画面へのアクセス。
* **お問い合わせ管理**: 問い合わせ一覧の閲覧、検索、詳細表示、削除、CSVエクスポート機能。
* **タグ管理**: 管理画面からお問い合わせに紐づくタグの追加、編集、削除機能。
* **公開API**: 外部からお問い合わせ情報の取得、作成、更新、削除、検索を行えるRESTful APIの実装。

## APIエンドポイント一覧

| メソッド | パス | 概要 |
| :--- | :--- | :--- |
| `GET` | `/api/v1/contacts` | お問い合わせ一覧取得（検索・ページネーション対応） |
| `GET` | `/api/v1/contacts/{id}` | お問い合わせ詳細取得 |
| `POST` | `/api/v1/contacts` | お問い合わせ新規作成 |
| `PUT` | `/api/v1/contacts/{id}` | お問い合わせ更新 |
| `DELETE` | `/api/v1/contacts/{id}` | お問い合わせ削除 |

## 開発環境
* **URL**: `http://localhost`

## 主な実装技術・構成
* **PHP**: 8.x
* **Framework**: Laravel
* **Database**: MySQL
* **Authentication**: Laravel Fortify
* **API**: JSON Resources
* **Utility**: CSV Export (BOM付き), Pagination

## 利用方法
1. **リポジトリのクローン**
   ```bash
   git clone <repository-url>
   cd <project-folder>
