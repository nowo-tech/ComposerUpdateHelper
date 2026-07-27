# Release process

1. Update [CHANGELOG.md](CHANGELOG.md) with the version and date.
2. Run `make release-check` and fix any reported issues (includes `check-no-cursor-coauthor` and `check-open-prs`).
3. Confirm `gh pr list --state open` is empty (REQ-REL-003), or every open PR has a valid hold.
4. Commit your changes.
5. Create an annotated tag: `git tag -a vX.Y.Z -m "Release vX.Y.Z"`.
6. Push commits and tags: `git push && git push --tags`.
7. Confirm the GitHub Release workflow created the release for the tag (or create it manually).
8. Ensure Packagist has picked up the new tag (if the package is registered).

After creating the release commit and tag, run `make check-no-cursor-coauthor` again **before** `git push` (REQ-GIT-001). The release commit itself is not covered by an earlier `release-check` run.
