<!--
  - ======
  - Header
  - ======
 -->

<!-- == Title == -->

 # Browser notifications demo

 <!-- == Description == -->

> Example implementation of web notifications using Service Worker, Push API and PHP.

<!--
  - ====
  - Body
  - ====
 -->

 <br>

## Installation

````bash
> git clone https://github.com/maximegelinas/browser-notifications-php-demo.git
> composer install
````

 <br>

## Usage

In the project directory run the following command:
````bash
> composer start
````
Then navigate to [http://localhost:8080/](http://localhost:8080/).

<br>

## Troubleshooting

If the demo is not working properly in your environment, open the Development Tools console, note the error message and see the section above that corresponds to your error. If there is no section for your error, please open an issue.

#### HTTP posts returns some PHP warnings in JSON data.
1. Set `always_populate_raw_post_data` to `-1` in in your `php.ini`.
2. Restart your web server.

#### CURL error 60: SSL certificate problem: unable to get local issuer certificate.
1. Download [cacert.pem](http://curl.haxx.se/ca/cacert.pem).
2. Put it here `<path to PHP>/extras/ssl/cacert.pem`.
3. Add `curl.cainfo = <path to PHP>/extras/ssl/cacert.pem` after the `[curl]` section in your `php.ini`.

<br>

## License

MIT