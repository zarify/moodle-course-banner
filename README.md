# Course banner plugin (local_coursebanner)

A Moodle local plugin that displays customizable banner images on course pages.

![An example banner](img/banner-plugin-example-output.png)

## Compatibility

**Supported Moodle versions:** 4.5 (LTS), 5.0, 5.1

## Installation
Either build a release using the shell script or download a zip release
from [releases](https://github.com/zarify/moodle-course-banner/releases)
and install the plugin zip via the Site Administration Plugins interface.

## Configuration

![Banner plugin settings in Plugins administration](img/banner-plugin-plugins.png)

1. Set the global banner height, optional default banner URL, and category/subcategory banner URLs in **Site administration → Plugins → Local plugins → Manage course banners**.
2. Optionally override per course in **Course settings → Course banner image URL**.

![Plugin configuration, detecting categories](img/banner-plugin-config.png)

![Override URL in course settings](img/banner-plugin-course-override.png)

### Resolution order

1. Course override (if set)
2. Closest category banner (walks up to parent categories)
3. Default banner URL
## Banner Image Specifications

The banner image is displayed at a centrally configurable height (default 80px) with full page width, using CSS `object-fit: cover`.

### Recommended Dimensions

- **Width:** 1600–2000px (minimum 1600px for sharpness)
- **Height:** 300–316px (maintains good aspect ratio)
- **Aspect Ratio:** Approximately 6:1

Example: 2000×316px

### Content Placement Guidelines

- Keep all important visual content in the **middle vertical band** of the image
- Avoid critical content within 50–60px of the top or bottom edges—these will be cropped
- Decorative elements can extend to the full image boundaries

## Build and package

From the repository root, run:

```bash
./build_plugin.sh
```

This will:

1. Minify AMD JavaScript from `amd/src/*.js` into `amd/build/*.min.js`
2. Create `local_coursebanner.zip` ready for installation
