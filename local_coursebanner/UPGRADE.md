# Upgrade Guide for local_coursebanner

This guide provides version-specific upgrade instructions for the Course Banner plugin.

## Table of Contents
- [Upgrading to Moodle 5.0](#upgrading-to-moodle-50)
- [Upgrading to Moodle 5.1](#upgrading-to-moodle-51)
- [Compatibility Summary](#compatibility-summary)

---

## Upgrading to Moodle 5.0

After upgrading Moodle core to 5.0:

1. Visit **Site administration → Notifications** to complete any plugin database upgrades
2. **Purge all caches**: Site administration → Development → Purge all caches
3. **Verify functionality**
   - Check that banners display correctly on course pages
   - Verify the admin settings pages still work
   - Test editing course banner URLs in course settings

**Note**: The plugin is compatible with Moodle 5.0 and requires no file changes. CSS is compatible with Bootstrap 5.

---

## Upgrading to Moodle 5.1

### Plugin Directory Change

Moodle 5.1 relocates plugins to a `/public` subdirectory. After upgrading Moodle core to 5.1:

**If using plugin installation interface** (recommended):
- The plugin will automatically be installed to `<moodle>/public/local/coursebanner`

**If manually upgrading an existing installation**:
```bash
mv local/coursebanner public/local/coursebanner
```

### Complete the Upgrade

1. Visit **Site administration → Notifications** to complete any plugin database upgrades
2. **Purge all caches**: Site administration → Development → Purge all caches
3. **Verify functionality**
   - Check that banners display correctly on course pages
   - Verify the admin settings pages still work
   - Test editing course banner URLs in course settings

---

## Compatibility Summary

| Moodle Version | Plugin Location |
|----------------|----------------|
| 4.5.x (LTS) | `local/coursebanner` |
| 5.0.x | `local/coursebanner` |
| 5.1.x | `public/local/coursebanner` |
