# Testing

## Coverage
- **PHPUnit:** Banner resolver precedence (course override → category → parent category → default → empty).
- **PHPUnit:** Resolver edge cases (missing course fallback, deep category ancestry, URL trimming).
- **PHPUnit:** Course override save security (requires `local/coursebanner:edit`) and cache invalidation correctness.
- **Behat:** Admin category mapping page and course edit override driving the banner on course view pages.
- **Behat:** Permission checks for editing-teacher field visibility and manager access to manage page.

## Prerequisites
- Plugin installed at `<moodle>/public/local/coursebanner` (5.1+ layout; `<moodle>/local/coursebanner` on 4.5).
- PHPUnit config in `config.php` (`$CFG->phpunit_dataroot`, `$CFG->phpunit_prefix`).
- Behat config in `config.php` (`$CFG->behat_dataroot`, `$CFG->behat_prefix`) and a configured WebDriver/Selenium profile.

## Run PHPUnit
From the Moodle root. Paths below use the Moodle 5.1+ layout, where the webroot is `public/`;
on 4.5 drop the `public/` prefix (`admin/tool/phpunit/cli/util.php`, `local/coursebanner/tests/…`).

```bash
php public/admin/tool/phpunit/cli/util.php --install
vendor/bin/phpunit public/local/coursebanner/tests/banner_resolver_test.php
vendor/bin/phpunit public/local/coursebanner/tests/course_override_save_test.php
```

Re-run the `--install` step after plugin install/upgrade or when adding tests. Name test files
individually: the PHPUnit CLI matches `*Test.php` when given a directory, which would skip
Moodle's `*_test.php` files.

## Run Behat
From the Moodle root (5.1+ paths):

```bash
php public/admin/tool/behat/cli/init.php
php public/admin/tool/behat/cli/run.php --tags="@local_coursebanner"
```

`init.php` installs the acceptance-test site and enables the environment. If the container or
host running the tests has no internet access, pass `--no-composer-self-update
--no-composer-upgrade` so Composer is not invoked.
