
# Period Listo

Period Listo is a rapidly developped application based on Symfony, that provides a REST API called newLeave.
This application is developped as a technical test for the company Listo Paye.


## Requirements

To run and deploy easily this app, your environment should include :

- [Docker](https://www.docker.com/) v20.10
- [Docker-compose](https://github.com/docker/compose) v2.11
- [PHP](https://www.php.net/releases/8.1/en.php) v8.1 (only if you want to run unit tests locally)
## Run Locally

Clone the project

```bash
  git clone https://github.com/gwendalaubert/period_listo
```

Go to the project directory

```bash
  cd period_listo
```

Build the image

```bash
  docker-compose build
```

:bulb: If [Buildkit](https://docs.docker.com/build/buildkit/#getting-started) has been installed with Docker, you can use it as a more efficient builder

```bash
  BUILDKIT=1 docker-compose build
```

Start the project

```bash
  docker-compose up -d
```

Stop the project whend needed

```bash
  docker-compose down
```

## API Reference

#### NewLeave

```http
  POST /api/new-leave
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `start`   | `string` | **Required**. Date and time of start of the period (should match RFC3339 requirements)|
| `end`     | `string` | **Required**. Date and time of end of the period (should match RFC3339 requirements)  |

Takes two string parsed dates using RFC3339 format and returns monthly compliant periods that matches these dates.


## Usage/Examples

#### NewLeave

Request

```bash
curl --location --request POST 'http://localhost:81/api/new-leave' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'start=2022-11-21T12:00:00' \
--data-urlencode 'end=2022-12-15T15:00:00'
```

Response

```json
{
    "code": "000",
    "periods": [
        {
            "type": "leave",
            "start": "2022-11-21T12:00:00+00:00",
            "end": "2022-11-30T23:59:59+00:00"
        },
        {
            "type": "leave",
            "start": "2022-12-01T00:00:00+00:00",
            "end": "2022-12-15T23:59:59+00:00"
        }
    ]
}
```
## Running Tests

To run tests in Docker, run the following command

```bash
  docker exec -it php-apache php bin/phpunit
```

To run tests locally, run the following command

```bash
  php bin/phpunit
```

## Authors

- [@gwendalaubert](https://www.github.com/gwendalaubert)

