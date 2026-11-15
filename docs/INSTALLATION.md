# Installing Nadine

Nadine is a standard Moodle theme plugin. It does not patch core and owns no
database tables, so it can be installed, upgraded and removed like any other
plugin.

## Requirements

| Item | Requirement |
| --- | --- |
| Moodle | 5.3 (`2026100500`) or later in branch 503 |
| Parent theme | `theme_boost`, shipped with Moodle |
| PHP | The version required by your Moodle release |
| Database | Any database supported by Moodle 5.3 |
| Outbound network | Not required. No CDN, web font or external asset is loaded. |

## Install from the ZIP

1. Sign in as a site administrator.
2. Go to **Site administration → Plugins → Install plugins**.
3. Upload `theme_nadine.zip` and select **Theme** as the plugin type.
4. Complete the installation and the upgrade screen.
5. Go to **Site administration → Appearance → Themes → Theme selector** and
   choose **Nadine** for the device types you want.

## Install from a directory

```bash
# Moodle 5.x keeps the web root in public/.
cp -R nadine /path/to/moodle/public/theme/nadine
php /path/to/moodle/admin/cli/upgrade.php --non-interactive
php /path/to/moodle/admin/cli/purge_caches.php
```

Then select the theme in the theme selector.

## First-run checklist

1. **Theme selector** – set Nadine as the default theme.
2. **Branding** – upload a logo and favicon, and confirm the accent colour.
3. **Homepage** – review the hero copy and decide whether the search field
   should be shown.
4. **Dashboard** – decide whether the welcome band is shown.
5. **Appearance** – choose the default colour mode. **System** respects the
   visitor's operating-system preference.
6. **Footer** – set the tagline and the short description.
7. Purge caches if you edited files directly rather than through the settings
   forms.

## Icon set

The theme ships a complete favicon set in `pix/`, linked from the page head by
`standard_head_html()`:

| File | Purpose |
| --- | --- |
| `favicon.ico` | 48/32/16 — the fallback every browser understands |
| `favicon.svg` | Scalable icon for browsers that prefer it |
| `favicon-96x96.png` | Pinned tab in Windows browsers |
| `apple-touch-icon.png` | 180×180 home-screen icon on iOS |
| `web-app-manifest-192x192.png`, `web-app-manifest-512x512.png` | Maskable icons for an installed web app |
| `site.webmanifest` | Web app manifest naming the installed app |

The manifest carries the theme's own name and a white `theme_color` and
`background_color` to match the icons. Replace `pix/site.webmanifest` if the
site installs as a web app under a different name. Uploading a favicon in the
theme settings replaces all of this with that one file.

## Upgrading

Replace the `nadine` directory (or upload a newer ZIP) and run the Moodle
upgrade. Theme settings are stored in the `config_plugins` table and file areas,
so they survive an upgrade. Purge caches after upgrading so that compiled SCSS
and Mustache templates are regenerated.

## Uninstalling

1. Switch the theme selector back to Boost.
2. Go to **Site administration → Plugins → Plugins overview → Themes** and
   uninstall Nadine.

Uninstalling removes the theme's settings and uploaded files. Course content,
users, grades and activity data are untouched.

## Troubleshooting

**The site looks unstyled after installing.**
Moodle compiles the theme SCSS on the first request after installation. On a
slow machine the first compile can take several minutes; subsequent requests are
served from `moodledata/cache/theme`. If the page renders without CSS, purge
caches and reload once.

**The logo does not appear.**
Check that the file was uploaded under **Branding → Logo**. If no logo is set,
the theme falls back to the site logo, then to the built-in mark plus the site
name.

**The dark mode switch is missing.**
The switch is only shown when **Appearance → Enable light and dark appearance**
is on. Signed-out visitors use the default mode configured on the same page.

**Arabic text uses the wrong font.**
Nadine asks for Noto Naskh Arabic and Noto Kufi Arabic before falling back to
the system Arabic font. Install those fonts on the client if you want the exact
intended rendering; no font is downloaded from a third party.

**Changes to SCSS or templates do not show.**
Run `php admin/cli/purge_caches.php`. In a production environment with a theme
cache, also confirm that `moodledata` is writable by the web server user.
