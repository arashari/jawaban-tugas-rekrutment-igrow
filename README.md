# Table of Contents
- [Table of Contents](#table-of-contents)
- [Database Design](#database-design)
  - [pembiayaan](#pembiayaan)
  - [rencana pembayaran (pembiayaan)](#rencana-pembayaran-pembiayaan)
- [Redis Design](#redis-design)
  - [pembiayaan](#pembiayaan-1)
- [API Design](#api-design)
  - [create pembiayaan](#create-pembiayaan)
    - [request](#request)
    - [response](#response)
  - [get detail pembiayaan](#get-detail-pembiayaan)
    - [request](#request-1)
    - [response](#response-1)
- [Limitasi dan Asumsi](#limitasi-dan-asumsi)
- [Changes Files](#changes-files)
- [References](#references)

# Database Design
## pembiayaan

| column | type | keterangan |
| --- | --- | --- |
| id | int | PK |
| code | string(5) | id pembiayaan, unique, 5 karakter alphabet uppercase (A-Z) |
| plafond | int | nominal plafond |
| mpt | int | margin per year |
| tenor | int | tenor dalam bulan |
| pi_pokok | int | payment interval (dalam bulan) untuk pokok |
| pi_margin | int | payment interval (dalam bulan) untuk margin |
| start_date | date | YYYY-MM-DD, tanggal mulai pembiayaan |
| end_date | date | YYYY-MM-DD, tanggal pembiayaan berakhir |

## rencana pembayaran (pembiayaan)

| column | type | keterangan |
| --- | --- | --- |
| id | int | PK |
| id_pembiayaan | int | FK |
| payment_date | date | YYYY-MM-DD, tanggal rencana pembayaran |
| pokok | int | jumlah kewajiban pokok |
| margin | int | jumlah kewajiban margin |

# Redis Design

## pembiayaan
- key -> "pembiayaan:" + kode pembiayaan
- value -> data api response
- durasi cache -> 1 hari

# API Design

- response template

| field | type | keterangan |
| --- | --- | --- |
| code | int | response code |
| message | string | response message (raw) |
| data | any |  |

- response code

| code | keterangan |
| --- | --- |
| 200 | success |
| 404 | data not found |
| 422 | validation error, misal karena kurang parameter required ketika request |
| 500 | server error, something wrong |

## create pembiayaan

- POST /api/v1/pembiayaan

### request

| field | type | keterangan |
| --- | --- | --- |
| plafond | int | pokok pembiayaan |
| mpt | int | margin per tahun |
| tenor | int | dalam bulan |
| pi_pokok | int | |
| pi_margin | int | |
| start_date | string | YYYY-MM-DD |

### response

| field | type | keterangan |
| --- | --- | --- |
| code | string | kode pembiayaan |
## get detail pembiayaan

- GET /api/v1/pembiayaan/{id}?refresh

### request

| field | type | keterangan |
| --- | --- | --- |
| id | string | kode pembiayaan |
| refresh | any | opsional, flag untuk memaksa update ulang cache |
### response

| field | type | keterangan |
| --- | --- | --- |
| data | object | berisi object pembiayaan beserta relasinya dengan rencana pembiayaan |

# Limitasi dan Asumsi

- fungsi generator kode pembiayaan tidak mengecek apakah kode tersebut sudah pernah tergenerate sebelumnya
- tenor diasumsikan pasti kelipatan dari periode pembayaran pokok dan margin
  - artinya tidak ada kasus seperti -> tenor: 12 periode pembayaran: 5
  - atau -> tenor: 13 periode pembayaran: 2

# Changes Files

- [Procfile](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/Procfile)
- [README.md](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/README.md)
- [app/Http/Controllers/PembiayaanController.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Http/Controllers/PembiayaanController.php)
- [app/Http/Requests/PembiayaanStoreRequest.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Http/Requests/PembiayaanStoreRequest.php)
- [app/Models/Pembiayaan.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Models/Pembiayaan.php) <<< PERSONAL HIGHLIGHT
- [app/Models/RencanaPembayaran.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Models/RencanaPembayaran.php)
- [app/Providers/AppServiceProvider.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Providers/AppServiceProvider.php) <<< PERSONAL HIGHLIGHT
- [app/Providers/HelpersProvider.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/Providers/HelpersProvider.php)
- [app/helpers.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/app/helpers.php) <<< PERSONAL HIGHLIGHT
- [composer.json](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/composer.json)
- [composer.lock](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/composer.lock)
- [config/app.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/config/app.php)
- [config/database.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/config/database.php)
- [database/migrations/2022_03_05_011836_create_pembiayaans_table.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/database/migrations/2022_03_05_011836_create_pembiayaans_table.php)
- [database/migrations/2022_03_05_012233_create_rencana_pembayarans_table.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/database/migrations/2022_03_05_012233_create_rencana_pembayarans_table.php)
- [routes/api.php](https://github.com/arashari/jawaban-tugas-rekrutment-igrow/blob/master/routes/api.php)

> *PERSONAL HIGHLIGHT: berisi sesuatu yang menurut saya pribadi keren atau indah
# References

- [add redis support to laravel](https://devcenter.heroku.com/articles/php-support#using-optional-extensions)
