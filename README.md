DepDoc helps you document the dependencies of your project, currently supporting Composer and Node packages.

# Usage

## Creating and updating dependencies file

```
./vendor/bin/depdoc update
```

Creates and updates a DEPENDENCIES.md file in your repository, which contains every installed Composer and Node package, its version and description, and offers you a way to document why and how you use this package.

By adding a lock emoji (🔒) after the version number, you can document that this package should not be updated. Alternatively you can use 🛇, ⚠, or ✋.

## Validating dependencies file

```
./vendor/bin/depdoc validate
```

Validates that every installed dependency is documented in DEPENDENCIES.md. Also makes sure that no package surpasses its locked version.
