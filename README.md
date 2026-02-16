
🟢 PGoldApp – Cryptocurrency Trading API
Overview




This is a RESTful cryptocurrency trading API built with Laravel 12.
Users can:



Register and authenticate

Manage a Naira wallet

Buy and sell BTC, ETH, USDT

View transaction history




🛠 Tech Stack

Laravel 12

Sanctum (Authentication)

Postgres

CoinGecko API

PHPUnit





⚙ Setup Instructions
git clone https://github.com/AdeDVictorious/pgoldapp.git

cd pgoldapp

composer install

cp .env.example .env

php artisan key:generate

//cron job 
php artisan queue:work

cp .env.testing for test 

//for testing purpose .i.e to run the test file
php artisan test


Configure database in .env

php artisan migrate --seed
php artisan serve






🔐 Authentication

Uses Laravel Sanctum.

Register

POST /api/register

Login

POST /api/login

Returns Bearer token.




💰 Wallet System

Each user has:

1 Naira wallet

3 crypto wallets (BTC, ETH, USDT)


All wallet movements are tracked in wallet_transactions for all related naira transaction.


All crypto wallet movements are tracked in trade for all related crypto transaction.



📈 Trading Logic
Buy Flow

Fetch rate from CoinGecko

Calculate crypto amount

Apply 2.5% fee

Deduct Naira (amount + fee)

Credit crypto wallet

Record trade

Sell Flow

Check crypto balance

Convert to Naira

Deduct 2.5% fee

Credit Naira wallet

Record trade




💸 Fee Structure

2.5% fee on buy

2.5% fee on sell

Fee applied on Naira side

Rounded to 4 decimal places



🌍 CoinGecko Integration

Integrated with CoinGecko

Free tier API used

Rates cached for 60 seconds

Failures handled gracefully

API mocked in tests

🧪 Running Tests

php artisan test




Tests cover:

Buy flow

Sell flow

Insufficient balance





📂 Architecture Decisions

Business logic separated into services

Database transactions used for financial integrity

Wallet transactions stored separately for audit trail

Crypto balances stored independently from Naira wallet



⚖ Trade-offs

Due to time constraints:

queue system for background jobs during user registration for crypto wallet creation

No event sourcing

No Redis caching

Basic exception handling 



⏱ Time Spent

Approximately 25-35 hours.



Postman doc: https://documenter.getpostman.com/view/34843579/2sBXcBohwh 



postman endpoint link : https://app.getpostman.com/join-team?invite_code=78197d0652b59d17c642d1215e7605bc73bbec00ace44c0119b4ec417b44bb22&target_code=eb0afa42e6df0958fc0cdc3ef866860c

