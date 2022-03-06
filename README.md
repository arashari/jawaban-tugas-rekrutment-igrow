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

## rencana pembayaran pengembalian dana
- key -> kode pembiayaan
- value -> [{ d, p, sp, m, sm, t}]
  - d: (date) tanggal pembayaran
  - p: (pokok) kewajiban pokok
  - sp: (sisa pokok) sisa kewajiban pokok
  - m: (margin) kewajiban margin
  - sm: (sisa margin) sisa kewajiban margin
  - t: (total) total kewajiban
  - *jika "date" == null, artinya dia adalah row "total"
- durasi cache -> harian

# API Design

- response template

| field | type | keterangan |
| --- | --- | --- |
| code | int | response code |
| message | string | response message (raw) |
| data | any | data yang sebenarnya |

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
| id | string | kode pembiayaan |
## get detail pembiayaan

- GET /api/v1/pembiayaan/{id}

### request

| field | type | keterangan |
| --- | --- | --- |
| id | string | kode pembiayaan |
### response

| field | type | keterangan |
| --- | --- | --- |
| pembiayaan | object | berisi detail pembiayaan |
| pembayaran | array | array of object rencana pembayaran |

## Limitations

- code generator tidak mengecek apakah code sudah pernah tergenerate sebelumnya
- tenor diasumsikan pasti kelipatan dari periode pembayaran pokok dan margin

---

<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 2000 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[OP.GG](https://op.gg)**
- **[WebReinvent](https://webreinvent.com/?utm_source=laravel&utm_medium=github&utm_campaign=patreon-sponsors)**
- **[Lendio](https://lendio.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
