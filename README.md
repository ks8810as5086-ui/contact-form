# COACHTECH お問い合わせフォーム

## 概要
本プロジェクトは、ユーザーが問い合わせ内容を送信し、管理者がその内容を閲覧・管理できる「お問い合わせフォームシステム」です。
フロントエンドのフォーム機能に加え、管理者用の管理画面（認証機能付き）、および外部システムとの連携を想定した公開API機能を実装しています。

### 主な機能
* **お問い合わせフォーム**: ユーザーによる問い合わせ内容の送信および確認機能。
* **管理者認証**: ユーザー登録・ログインによるセキュアな管理画面へのアクセス。
* **お問い合わせ管理**: 問い合わせ一覧の閲覧、検索、詳細表示、削除、CSVエクスポート機能。
* **タグ管理**: 管理画面からお問い合わせに紐づくタグの追加・編集・削除機能。
* **公開API**: 外部からお問い合わせ情報の取得・作成・更新・削除・検索を行えるRESTful APIの実装。

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
