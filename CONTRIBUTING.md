# Contribution Guidelines

We will always have a need for developers to help us improve Solder. There is no such thing as a perfect project and
things can always be improved. If you are a developer and are interested in helping then please do not hesitate. Just
make sure you follow our guidelines.

## General steps

1. Set up your workspace as described in [Getting Started](https://docs.solder.io/reference/getting-started).

2. Check for existing issues in the [TechnicSolder](https://github.com/TechnicPack/TechnicSolder/issues) repository.
   There is possibly someone else already working on the same thing.

3. If the issue requires a bigger change you may want to submit the issues without the necessary changes first, so we
   can confirm the issue and know that you're working on fixing it. You should also create a WIP (work in process) pull
   request prefixed with ``[WIP]`` early so we can already start reviewing them.

4. Fork the project, clone it and make your changes in an extra branch with a proper branch name ``bugfix-``,
   ``patch-``, ``feature-``.

5. Test your changes using phpunit, commit and push them to your fork.

6. Submit the pull request to the dev branch with a short summary what you've changed and why it should be changed in
   that way.

7. If you make additional changes, push new commits to your branch. **Do not squash your changes**, that makes it
   extremely hard to see what you've changed compared to the previous version of your pull request.

## Developer's Certificate of Origin

This repository uses the Developer's Certificate of Origin:

```
Developer Certificate of Origin
Version 1.1

Copyright (C) 2004, 2006 The Linux Foundation and its contributors.

Everyone is permitted to copy and distribute verbatim copies of this
license document, but changing it is not allowed.


Developer's Certificate of Origin 1.1

By making a contribution to this project, I certify that:

(a) The contribution was created in whole or in part by me and I
    have the right to submit it under the open source license
    indicated in the file; or

(b) The contribution is based upon previous work that, to the best
    of my knowledge, is covered under an appropriate open source
    license and I have the right under that license to submit that
    work with modifications, whether created in whole or in part
    by me, under the same open source license (unless I am
    permitted to submit under a different license), as indicated
    in the file; or

(c) The contribution was provided directly to me by some other
    person who certified (a), (b) or (c) and I have not modified
    it.

(d) I understand and agree that this project and the contribution
    are public and that a record of the contribution (including all
    personal information I submit with it, including my sign-off) is
    maintained indefinitely and may be redistributed consistent with
    this project or the open source license(s) involved.
```
