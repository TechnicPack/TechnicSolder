# Laravel Boost

## Tools

Prefer Boost MCP tools over manual alternatives like shell commands or file reads.

- `database-query` — read-only queries instead of raw SQL in tinker
- `database-schema` — inspect table structure before writing migrations or models
- `get-absolute-url` — resolve correct scheme/domain/port before sharing URLs with the user
- `browser-logs` — read browser logs/errors; only recent entries are useful

## Searching Documentation

Always use `search-docs` before making code changes. Do not skip this step. It returns version-specific docs based on installed packages automatically.

- Pass a `packages` array to scope results when you know which packages are relevant.
- Use multiple broad, topic-based queries: `['rate limiting', 'routing rate limiting', 'routing']`. Expect the most relevant results first.
- Do not add package names to queries — package info is already shared. Use `test resource table`, not `filament 4 test resource table`.

### Search Syntax

1. Words for auto-stemmed AND: `rate limit` matches "rate" AND "limit"
2. `"quoted phrases"` for exact matching: `"infinite scroll"` requires adjacent words in order
3. Combine words and phrases: `middleware "rate limit"`
4. Multiple queries for OR: `queries=["authentication", "middleware"]`
