## Environment Setup

### Initial steps

1. [Install Docker](https://www.docker.com/get-started)
2. Clone this project

### Environment configuration

1. Create a local environment file (`cp .env .env.local`) if you want to modify any parameter

## Usage

First of all you should execute:

```
make init_db
```

Next, setup local environment by executing:

```
make prepare-local
```

And then start local environment:

```
make start-local
```

And then call `http://localhost:8080/health-check` to check all is ok.