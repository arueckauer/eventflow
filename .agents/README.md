# Skills

This directory contains reusable agent skills.

## Structure (Best Practice)

- **One folder per skill** directly under `.agents/skills/`.
- Each skill folder contains a **`SKILL.md`** file (required).
- Folder name and skill `name` should be `kebab-case`, unique, and descriptive.
- For thematic grouping, use domain prefixes (for example `git-...`) instead of deep subfolder hierarchies.
- Use a consistent skill template:
  - YAML frontmatter with `name`, `description` (optional `license`, `allowed-tools`)
  - Markdown body with rules, process, and output format

## Current Skills

- [`git-conventional-commit`](skills/git-conventional-commit/SKILL.md): Generates a [Conventional Commit](https://www.conventionalcommits.org/en/v1.0.0/) message for staged changes. Before committing, the user chooses whether to accept the proposed message and commit or cancel, copy the message, optionally edit it, and commit manually. The skill never changes which files are staged.
