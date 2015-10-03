# Contribution Guidelines

We will always have a need for developers to help us improve Solder. There is no such thing as a perfect project and things can always be improved. If you are a developer and are interested in helping then please do not hesitate. Just make sure you follow our guidelines.

General steps
=============

1. Setup your workspace as described in [Getting Started](http://docs.solder.io/v0.7/docs/getting-started).

2. Check for existing issues in the [TechnicSolder](https://github.com/TechnicPack/TechnicSolder/issues) repository. There is possibly someone else already working on the same thing. 

3. If the issue requires a bigger change you may want to submit the issues without the necessary changes first, so we can confirm the issue and know that you're working on fixing it. You should also create a WIP (work in process) pull request prefixed with ``[WIP]`` early so we can already start reviewing them.

4. Fork the project, clone it and make your changes in an extra branch with a proper branch name ``bugfix-``, ``patch-``, ``feature-``.

5. Test your changes using phpunit, commit and push them to your fork.

6. Submit the pull request to the dev branch with a short summary what you've changed and why it should be changed in that way.

7. If you make additional changes, push new commits to your branch. **Do not squash your changes**, that makes it extremely hard to see what you've changed compared to the previous version of your pull request.
