# My awesome Web application

Hello developer! Welcome to your new awesome `Web` application built with the Spiral framework.

We're excited that you've chosen Spiral for your project and we hope that our installer package has made the
installation process a breeze.

To help you get started, we've provided some instructions for configuring the individual packages that were installed.
Depending on the packages you chose during the installation, you'll find the following next steps:

## Next Steps

## Configuration
1. Please, configure the environment variables in the .env file at the application's root.
2. Read documentation about Spiral Framework: https://spiral.dev/docs
### RoadRunnerBridge
1. The settings for RoadRunner are in a file named .rr.yaml at the main folder of the app.
2. Documentation: https://spiral.dev/docs/start-server
### StemplerBridge
1. Read more about views in the Spiral Framework: https://spiral.dev/docs/views-configuration
2. Documentation: https://spiral.dev/docs/views-stempler
### LeagueEvent
1. Documentation: https://spiral.dev/docs/advanced-events

## Usage

To start HTTP server using RoadRunner, run the following command in your project directory:

```bash
./rr serve
```

Once the server is running, you can access your application in a web browser by going to the following
URL: http://127.0.0.1:8080.

> **Note**:
> For more information on how to use RoadRunner with Spiral, please consult
> the [official documentation](https://spiral.dev/docs/start-server).

## Console commands

### Download or update RoadRunner

Allows to install the latest version of the RoadRunner compatible with your environment (operating system, processor
architecture, runtime, etc...).

```bash
composer rr:download
# or
./vendor/bin/rr get-binary
```

### Download or update protoc-gen GRPC plugin

Allows to install the latest version of the `protoc-gen-php-grpc` file compatible with your environment (operating
system, processor architecture, runtime, etc...).

```bash
composer rr:download-protoc
# or
./vendor/bin/rr download-protoc-binary
```

### Generate gRPC proto files

To generate gRPC proto files, run the following command:

```bash
php app.php grpc:generate
```

## Project Structure

If you chose to install the default application skeleton, your project will have the following directory structure:

```
- Endpoint
    - Web
        - UserController.php
        - Filter
            - CreateUserFilter.php
        - Middleware
            - LocaleMiddleware.php
        - Interceptor
            - ValidateFiltersInterceptor.php
        - routes.php
    - Console
        - Interceptor
            - PromptRequiredArguments.php
        - CreateUserCommand.php
    - RPC
        - ...
    - Temporal
        - Workflow
            - ...
        - Activity
            - ...
- Application
    - Bootloader
        - RoutesBootloader.php
        - UserModuleBootloader.php
    - Exception
        - SomeException.php
        - Renderer
            - ViewRenderer.php
    - AppDirectories.php
    - Kernel.php
- Domain
    - User
        - Entity
            - User.php
        - Service
            - StoreUserService.php
        - Repository
            - UserRepositoryInterface.php
        - Exception
            - UserNotFoundException.php
- Infrastructure
    - Persistence
        - CycleUserRepository.php
    - CycleORM
        - Typecaster
            - UuidTypecast.php
    - Interceptor
        - LogInterceptor.php
        - ExceptionHandlerInterceptor.php
```

#### Here's a brief explanation of the directories and files in this structure:

- **Endpoint**: This directory contains the entry points for your application, including HTTP endpoints (in the Web
  subdirectory), command-line interfaces (in the Console subdirectory), and gRPC services (in the RPC subdirectory).

- **Application**: This directory contains the core of your application, including the Kernel class that boots your
  application, the Bootloader classes that register services with the container, and the Exception directory that
  contains exception handling logic.

- **Domain**: This directory contains your domain logic, organized by subdomains. For example, an Entity for the User
  model, a Service for storing new users, a Repository for fetching users from the database, and an Exception for
  handling user-related errors.

- **Infrastructure**: This directory contains the infrastructure code for your application, including the Persistence
  directory for database-related code, the CycleORM directory for ORM-related code, and the Interceptor directory for
  global interceptors.

The project structure we provided is a common structure used in many PHP applications, and it can serve as a starting
point for your projects By following this structure, you can organize your code in a logical and maintainable
way, making it easier to build and scale your applications over time. Of course, you may need to make adjustments to fit
the specific needs of your project, but this structure provides a solid foundation for most applications.

**Good luck with your project!**

## Useful resources

- [**Spiral Framework documentation**](https://spiral.dev/docs)
- [**RoadRunner documentation**](https://roadrunner.dev/docs)
- [Community packages](https://github.com/spiral-packages).
- [Birddog](https://github.com/roadrunner-server/birddog) — OpenSource tool for monitoring RoadRunner instances.
- [Support us](https://github.com/sponsors/roadrunner-server)
- [Contributing](https://spiral.dev/docs/about-contributing/)

## Support

If you have any questions or need help with the project, please don't hesitate to reach out! You can find us on Discord
at the following link:

[Discord Server](https://discord.gg/TFeEmCs)

Alternatively, you can create an issue on GitHub to report a bug or request a feature:

[Create an Issue on GitHub](https://github.com/spiral/framework/issues/new/choose)

We welcome any feedback or suggestions you may have, and are always happy to help troubleshoot any issues you may
encounter.
