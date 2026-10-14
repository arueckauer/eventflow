# Agent Instructions

These instructions apply throughout this repository. See the [README](README.md) for project architecture, setup, and tool usage.

## Working Practices

- Complete the requested task and follow established code and test patterns.
- Preserve unrelated work and avoid scope creep.
- Respect the architecture boundaries: `Domain` must not depend on `Application` or `Infrastructure`; `Application` may depend on `Domain`; `Infrastructure` may depend on both.
- For domain value objects, use `SeatNumber` and its tests as the reference for immutability, named constructors, behavior-focused tests, and mutation coverage.
- Do not weaken, bypass, or skip quality checks to make them pass. If you identify a missing or unsuitable check, explain the recommended CI or quality-tool change and ask the user before applying it.

## Validation

- Run project quality tools in the project container, not directly in WSL. The WSL environment may not have all required PHP extensions; in particular, Infection requires Xdebug for coverage.
- Build or refresh the image from the repository root when needed:

  ```bash
  docker build -t eventflow-ci -f Containerfile .
  ```

- Run the full CI quality gate from the repository root. With no script argument, the container runs `composer ci`, including all six checks:

  ```bash
  docker run --rm --user "$(id -u):$(id -g)" \
    -e COMPOSER_CACHE_DIR=/tmp/composer-cache \
    -v "$PWD":/app:Z \
    eventflow-ci
  ```

  - Run an individual check by passing its Composer script name as the final argument. The container entrypoint installs Composer dependencies before running the requested script:

    ```bash
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci coding-standard
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci static-analysis
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci architecture
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci tests
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci mutation-testing
    docker run --rm --user "$(id -u):$(id -g)" -e COMPOSER_CACHE_DIR=/tmp/composer-cache -v "$PWD":/app:Z eventflow-ci rector-check
    ```

- On systems without SELinux, omit `:Z` from the volume mount. Docker can be replaced with Podman if that is the available container engine.
- If a check fails, investigate and report the failure; do not assume it is an expected baseline without verifying the current branch and task context.
- For documentation-only changes, validation is not required unless documentation tests apply.

## Project Conventions

- The minimum supported PHP version is 8.5.7. The project container pins PHP 8.5.11.
