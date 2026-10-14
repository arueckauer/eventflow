# Working on EventFlow

These are the standing rules for any coding agent in this repository. The
task itself comes with each prompt; these rules apply to every task. If your
agent reads a different file (Claude Code, for instance, reads `CLAUDE.md`),
point that file to this one.

## The tests are the specification

- The tests in `tests/` are written by a human before you start. They define
  what is to be built.
- Do not change, delete, skip or add tests. If you think a test is wrong or a
  test is missing, stop and say so.
- Implement what the tests require and nothing more. Code that no test
  requires leaves mutants that no test kills, and that makes the quality gate
  fail.
- If mutants survive, report them. Do not write tests to kill them.

## Done means green

- Run `./tools/composer ci` before you report that you are done. Done means
  that it exits with status 0, all five stages.
- Do not make the gate green by weakening it: no changes to `composer.json`,
  `phpunit.xml`, `phpstan.neon`, `deptrac.yaml`, `infection.json5.dist`,
  `.php-cs-fixer.dist.php`, `rector.php` or `.github/`; no PHPStan baseline;
  no `@phpstan-ignore` and no `@infection-ignore-all`.
- If the same stage fails three times in a row, stop and report what you
  tried.
- Do not commit. A human reviews your changes and commits them.

## Architecture

- `Domain` depends on nothing. `Application` depends on `Domain` only.
  `Infrastructure` may depend on both. Deptrac enforces this.
- Ports are interfaces in `Domain`; adapters implement them in
  `Infrastructure`.
- Business rules belong to the domain. A use case in `Application`
  orchestrates; it does not decide.
- The domain does not read a clock. The current point in time is passed in
  as a parameter.
- Do not add Composer dependencies.

## Style

- `src/Domain/SeatNumber.php` and its test are the template.
- Value objects are `final readonly`, are created through a named
  constructor and have `equals()`.
- Aggregates record domain events with the `RecordsDomainEvents` trait;
  domain events implement `DomainEvent`.
- Exceptions implement the marker interface
  `Thephpcc\EventFlow\Domain\Exception` and are created through named
  constructors.
