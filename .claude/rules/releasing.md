# Releasing

Before creating a release tag:

1. Update `CHANGELOG.md` with the new version's changes under a `## [x.y.z] - YYYY-MM-DD` heading
2. Update `SOLDER_VERSION` in `app/Providers/AppServiceProvider.php`
3. Update the version in `docs/api/read/root.md` example response
4. Add a comparison link at the bottom of `CHANGELOG.md`: `[x.y.z]: https://github.com/TechnicPack/TechnicSolder/compare/vPREVIOUS...vx.y.z`
5. Commit these changes as `chore: release vx.y.z`
6. Create an annotated tag: `git tag -a vx.y.z -m "Version x.y.z"`

The GitHub release is created automatically from `CHANGELOG.md` by the release workflow when the tag is pushed.
