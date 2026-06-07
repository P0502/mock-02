## アプリケーション名
### 勤怠管理アプリ　COACHTECH

## アプリケーションの詳細

## 環境構築の手順
### 1.リポジトリをクローンする
#### $ git clone git@github.com:P0502/mock-02.git
### 2.環境構築をする(Dockerコンテナ作成)
#### $ docker-compose up -d --build
### 3.Laravelのパッケージのインストール
#### (1) $ docker-compose exec php bash(PHPコンテナに入る)
#### (2) composer install(PHPコンテナ内で実行する)
### 4. .envファイルの作成
#### cp .env.example .env
#### ※PHPコンテナ内で.env.exampleファイルをコピーして、ファイル名を.envに変更して作成する
### 5.マイグレーションを実行する
#### php artisan migrate
#### ※PHPコンテナ内で実行する
### 6.シーディングを実行する
#### php artisan db:seed
#### ※シーディングを実行すると、users(一般ユーザー)→attendances(勤怠)→breaks(休憩時間)の順にダミーデータが作成されます。
#### ※PHPコンテナ内で実行する
### 7.キーを生成する
#### php artisan key:generate
#### ※PHPコンテナ内で実行する
## テストケース実行
#### php artisan test
#### ※PHPコンテナ内で実行する
## 環境構築時の注意点(メール認証エラーの対策)
#### 会員登録やメール認証機能を使用する際、環境によっては送信元アドレスの未設定によるエラー('cannnot send message withouta sender address')が発生する場合があります。
#### 環境構築時、または.env変更時には必ず以下の2点を確認・実行してください。
### 1. .envファイルの設定確認
#### .envファイル(またはサーバーの環境変数)に、メールの送信元アドレスが正しく設定されているか確認してください。
#### 空白になっているとメール送信時にエラーになります。
#### .envファイル内
#### MAIL_FROM_ADRESS="hello@example.com" ※空白にしないこと
#### MAIL_FROM_NAME="\${APP_NAME}"
### 2.設定キャッシュのクリア
#### .envファイルの変更を反映されるため、設定後に必ず以下のコマンドを実行してください。
#### php artisan config:clear
#### ※PHPコンテナ内で実行する
## 管理者ログインと自動判定について
#### 本アプリケーションは、申請一覧のURLが一般ユーザー側、管理者側共に'/stamp_correction_request/list'と共通に設定されているため、
#### ログインしたメールアドレスを基準に、管理者か一般ユーザーかを自動で検知・判定しています。
### ・管理者アカウントの判定基準
#### 管理者対象のメールアドレス:'admin@example.com'
### ・ログイン時の挙動とリダイレクト
#### 管理者ログイン画面('/admin/login')からログインする際は、必ず上記のメールアドレス('admin@example.com')でログインしてください。
#### 上記以外のメールアドレスでログイン後に、申請一覧('/stamp_correction_request/list')へアクセス、またはリダイレクトされると、
#### 自動的に一般ユーザー側の申請一覧へリダイレクトしてしまいます。
## 休憩時間モデル(breaks)の命名規則に関する重要な注意点
#### 休憩時間を管理するモデルを実装する際、ファイル名の命名において仕様上の制約が発生したため、以下の通り例外的な命名を行っております。
### 1.発生した問題と理由
#### 通常、Laravelのモデル名は単数形で命名するのが標準的な規則です。
#### しかし、休憩時間を表す単数形の'break'は、PHP言語における予約語('break':文など、ループ処理を抜けるための構文)としてあらかじめ定義されています。
#### そのため、モデルファイル名を'break.php'として作成しようとすると、PHP構文解析でエラー(Fatal Error/Syntax Error)が発生し、プログラムを実行することができません。
### 2.問題の解決策
#### この予約語との衝突を回避するため、例外的に複数形を用いた以下の命名を採用しています。
#### ・モデルファイル名: 'Breaks.php'
#### ・対応するマイグレーションテーブル名: 'breaks'
## ER図
<img width="745" height="784" alt="スクリーンショット 2026-05-17 054452" src="https://github.com/user-attachments/assets/b0f6ba3d-a334-4682-9deb-1a3d303a7e99" />


## テーブル仕様書
#### 一般ユーザーテーブル
<img width="758" height="282" alt="スクリーンショット 2026-06-07 042357" src="https://github.com/user-attachments/assets/8f60ed23-b70d-45c7-92ec-62ef8b4ed260" />
<br><br>

#### 勤怠テーブル
<img width="759" height="215" alt="スクリーンショット 2026-06-07 042515" src="https://github.com/user-attachments/assets/912cadf7-0514-4b4e-86b4-2861d9dc6865" />
<br><br>

#### 休憩テーブル
<img width="760" height="173" alt="スクリーンショット 2026-06-07 042636" src="https://github.com/user-attachments/assets/2068952e-ac41-4431-bad6-0c12b3b4c6a1" />
<br><br>

#### 勤怠申請テーブル
<img width="759" height="353" alt="スクリーンショット 2026-06-07 042800" src="https://github.com/user-attachments/assets/54fd8867-7be9-4202-b8d1-c594759c9ebd" />


## 使用技術(実行環境)
#### ・PHP:8.1.34
#### ・Laravel:8.83.29
#### ・nginx:1.21.1
#### ・mysql:11.8.3
#### ・mailhog:1.0.1
#### ・phpmyadmin:8080:80

## URL
#### ・会員登録画面: http://localhost/register
#### ・ログイン画面: http://localhost/login
#### ・管理者ログイン画面: http://localhost/admin/login
#### ・phpmyadmin: http://localhost:8080/
