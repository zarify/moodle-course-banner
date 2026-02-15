# Testing

## Coverage
- **PHPUnit:** Banner resolver precedence (course override → category → parent category → default → empty).
- **PHPUnit:** Resolver edge cases (missing course fallback, deep category ancestry, URL trimming).
- **PHPUnit:** Course override save security (requires `local/coursebanner:edit`) and cache invalidation correctness.
- **Behat:** Admin category mapping page and course edit override driving the banner on course view pages.
- **Behat:** Permission checks for editing-teacher field visibility and manager access to manage page.

## Prerequisites
- Plugin installed at `<moodle>/local/coursebanner`.
- PHPUnit config in `config.php` (`$CFG->phpunit_dataroot`, `$CFG->phpunit_prefix`).
- Behat config in `config.php` (`$CFG->behat_dataroot`, `$CFG->behat_prefix`) and a configured WebDriver/Selenium profile.

## Run PHPUnit
From the Moodle root.

```bash
php admin/tool/phpunit/cli/util.php --install
vendor/bin/phpunit local/coursebanner/tests/banner_resolver_test.php
```

Re-run the `--install` step after plugin install/upgrade or when adding tests.

## Run Behat
From the Moodle root:

```bash
php admin/tool/behat/cli/util.php --enable
php admin/tool/behat/cli/run.php --tags="@local_coursebanner"
```
