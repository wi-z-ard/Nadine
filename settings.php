<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings for the Nadine theme.
 *
 * @package    theme_nadine
 * @copyright  2026 EduPro
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {
    $settings = new theme_boost_admin_settingspage_tabs(
        'themesettingnadine',
        get_string('configtitle', 'theme_nadine')
    );

    // Branding.
    $page = new admin_settingpage('theme_nadine_branding', get_string('brandingsettings', 'theme_nadine'));

    $setting = new admin_setting_configstoredfile(
        'theme_nadine/logo',
        get_string('logo', 'theme_nadine'),
        get_string('logo_desc', 'theme_nadine'),
        'logo',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.svg', '.webp'], 'maxbytes' => 2097152]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile(
        'theme_nadine/favicon',
        get_string('favicon', 'theme_nadine'),
        get_string('favicon_desc', 'theme_nadine'),
        'favicon',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.ico', '.png', '.svg'], 'maxbytes' => 524288]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new \theme_nadine\setting\colourtheme(
        'theme_nadine/colortheme',
        get_string('colortheme', 'theme_nadine'),
        get_string('colortheme_desc', 'theme_nadine'),
        'default',
        [
            'default' => get_string('colortheme_default', 'theme_nadine'),
            'forest' => get_string('colortheme_forest', 'theme_nadine'),
            'ocean' => get_string('colortheme_ocean', 'theme_nadine'),
            'plum' => get_string('colortheme_plum', 'theme_nadine'),
            'clay' => get_string('colortheme_clay', 'theme_nadine'),
            'slate' => get_string('colortheme_slate', 'theme_nadine'),
            'rose' => get_string('colortheme_rose', 'theme_nadine'),
            'amber' => get_string('colortheme_amber', 'theme_nadine'),
            'custom' => get_string('colortheme_custom', 'theme_nadine'),
        ]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_nadine/accentcolor',
        get_string('accentcolor', 'theme_nadine'),
        get_string('accentcolor_desc', 'theme_nadine'),
        '#ef5e00'
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configcolourpicker(
        'theme_nadine/primarycolor',
        get_string('primarycolor', 'theme_nadine'),
        get_string('primarycolor_desc', 'theme_nadine'),
        '#01275a'
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configselect(
        'theme_nadine/headerstyle',
        get_string('headerstyle', 'theme_nadine'),
        get_string('headerstyle_desc', 'theme_nadine'),
        'translucent',
        [
            'translucent' => get_string('headerstyle_translucent', 'theme_nadine'),
            'solid' => get_string('headerstyle_solid', 'theme_nadine'),
        ]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $setting = new admin_setting_configstoredfile(
        'theme_nadine/loginimage',
        get_string('loginimage', 'theme_nadine'),
        get_string('loginimage_desc', 'theme_nadine'),
        'loginimage',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.webp'], 'maxbytes' => 5242880]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_configtext(
        'theme_nadine/loginheading',
        get_string('loginheading', 'theme_nadine'),
        get_string('loginheading_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/loginintro',
        get_string('loginintro', 'theme_nadine'),
        get_string('loginintro_desc', 'theme_nadine'),
        '',
        PARAM_CLEANHTML,
        60,
        4
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/logineyebrow',
        get_string('logineyebrow', 'theme_nadine'),
        get_string('logineyebrow_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/loginimagealt',
        get_string('loginimagealt', 'theme_nadine'),
        get_string('loginimagealt_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $settings->add($page);

    // The two pickers only apply to the custom colour theme. The condition is
    // registered on the tab container, not the tab: theme_boost copies a tab's
    // settings into the container but not its dependencies, so a condition set
    // on the tab never reaches the page's JavaScript.
    $settings->hide_if('theme_nadine/accentcolor', 'theme_nadine/colortheme', 'eq', 'custom');
    $settings->hide_if('theme_nadine/primarycolor', 'theme_nadine/colortheme', 'eq', 'custom');

    // Homepage.
    $page = new admin_settingpage('theme_nadine_homepage', get_string('homepagesettings', 'theme_nadine'));

    $page->add(new admin_setting_heading(
        'theme_nadine/heroheading',
        get_string('heroheading', 'theme_nadine'),
        get_string('heroheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_nadine/enablehero',
        get_string('enablehero', 'theme_nadine'),
        get_string('enablehero_desc', 'theme_nadine'),
        1
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_nadine/enableherosearch',
        get_string('enableherosearch', 'theme_nadine'),
        get_string('enableherosearch_desc', 'theme_nadine'),
        1
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/heroeyebrow',
        get_string('heroeyebrow', 'theme_nadine'),
        get_string('heroeyebrow_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/herotitle',
        get_string('herotitle', 'theme_nadine'),
        get_string('herotitle_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/herotext',
        get_string('herotext', 'theme_nadine'),
        get_string('herotext_desc', 'theme_nadine'),
        '',
        PARAM_CLEANHTML,
        60,
        3
    ));

    $setting = new admin_setting_configstoredfile(
        'theme_nadine/heroimage',
        get_string('heroimage', 'theme_nadine'),
        get_string('heroimage_desc', 'theme_nadine'),
        'heroimage',
        0,
        ['maxfiles' => 1, 'accepted_types' => ['.png', '.jpg', '.jpeg', '.webp'], 'maxbytes' => 5242880]
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $page->add(new admin_setting_configtext(
        'theme_nadine/heroimagealt',
        get_string('heroimagealt', 'theme_nadine'),
        get_string('heroimagealt_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/heroprimarylabel',
        get_string('heroprimarylabel', 'theme_nadine'),
        get_string('heroprimarylabel_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_heading(
        'theme_nadine/heropanelheading',
        get_string('heropanelheading', 'theme_nadine'),
        get_string('heropanelheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/heropanelkicker',
        get_string('heropanelkicker', 'theme_nadine'),
        get_string('heropanelkicker_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/heropaneltext',
        get_string('heropaneltext', 'theme_nadine'),
        get_string('heropaneltext_desc', 'theme_nadine'),
        '',
        PARAM_TEXT,
        60,
        2
    ));

    $page->add(new admin_setting_heading(
        'theme_nadine/catalogueheading',
        get_string('catalogueheading', 'theme_nadine'),
        get_string('catalogueheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/catalogueeyebrow',
        get_string('catalogueeyebrow', 'theme_nadine'),
        get_string('catalogueeyebrow_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/cataloguetitle',
        get_string('cataloguetitle', 'theme_nadine'),
        get_string('cataloguetitle_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/catalogueintro',
        get_string('catalogueintro', 'theme_nadine'),
        get_string('catalogueintro_desc', 'theme_nadine'),
        '',
        PARAM_CLEANHTML,
        60,
        3
    ));

    $page->add(new admin_setting_heading(
        'theme_nadine/featuresheading',
        get_string('featuresheading', 'theme_nadine'),
        get_string('featuresheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/featureseyebrow',
        get_string('featureseyebrow', 'theme_nadine'),
        get_string('featureseyebrow_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/featurestitle',
        get_string('featurestitle', 'theme_nadine'),
        get_string('featurestitle_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    foreach ([1, 2, 3] as $index) {
        $page->add(new admin_setting_configtext(
            'theme_nadine/feature' . $index . 'title',
            get_string('featuretitle', 'theme_nadine', $index),
            get_string('featuretitle_desc', 'theme_nadine', $index),
            '',
            PARAM_TEXT
        ));

        $page->add(new admin_setting_configtextarea(
            'theme_nadine/feature' . $index . 'text',
            get_string('featuretext', 'theme_nadine', $index),
            get_string('featuretext_desc', 'theme_nadine', $index),
            '',
            PARAM_TEXT,
            60,
            2
        ));
    }

    $page->add(new admin_setting_heading(
        'theme_nadine/ctaheading',
        get_string('ctaheading', 'theme_nadine'),
        get_string('ctaheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/ctatitle',
        get_string('ctatitle', 'theme_nadine'),
        get_string('ctatitle_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/ctatext',
        get_string('ctatext', 'theme_nadine'),
        get_string('ctatext_desc', 'theme_nadine'),
        '',
        PARAM_TEXT,
        60,
        2
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/ctabrowselabel',
        get_string('ctabrowselabel', 'theme_nadine'),
        get_string('ctabrowselabel_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/ctajoinlabel',
        get_string('ctajoinlabel', 'theme_nadine'),
        get_string('ctajoinlabel_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configcheckbox(
        'theme_nadine/showfrontpagecontinue',
        get_string('showfrontpagecontinue', 'theme_nadine'),
        get_string('showfrontpagecontinue_desc', 'theme_nadine'),
        1
    ));

    $page->add(new admin_setting_configselect(
        'theme_nadine/homescrollanimation',
        get_string('homescrollanimation', 'theme_nadine'),
        get_string('homescrollanimation_desc', 'theme_nadine'),
        'none',
        [
            'none' => get_string('homescrollanimation_none', 'theme_nadine'),
            'reveal' => get_string('homescrollanimation_reveal', 'theme_nadine'),
            'aos' => get_string('homescrollanimation_aos', 'theme_nadine'),
        ]
    ));

    $settings->add($page);

    // Footer.
    $page = new admin_settingpage('theme_nadine_footer', get_string('footersettings', 'theme_nadine'));

    $page->add(new admin_setting_configtext(
        'theme_nadine/footertagline',
        get_string('footertagline', 'theme_nadine'),
        get_string('footertagline_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/footerabout',
        get_string('footerabout', 'theme_nadine'),
        get_string('footerabout_desc', 'theme_nadine'),
        '',
        PARAM_CLEANHTML,
        60,
        3
    ));

    $page->add(new admin_setting_heading(
        'theme_nadine/footersocialheading',
        get_string('footersocialheading', 'theme_nadine'),
        get_string('footersocialheading_desc', 'theme_nadine')
    ));

    // One field per network. An empty field hides that icon, so a site only
    // ever shows the accounts it actually keeps.
    foreach (['facebook', 'instagram', 'x', 'linkedin', 'youtube', 'tiktok', 'whatsapp', 'github'] as $network) {
        $page->add(new admin_setting_configtext(
            'theme_nadine/footersocial' . $network,
            get_string('footersocial' . $network, 'theme_nadine'),
            '',
            '',
            PARAM_URL
        ));
    }

    $settings->add($page);

    // Dashboard.
    $page = new admin_settingpage('theme_nadine_dashboard', get_string('dashboardsettings', 'theme_nadine'));

    $page->add(new admin_setting_configcheckbox(
        'theme_nadine/showdashboardwelcome',
        get_string('showdashboardwelcome', 'theme_nadine'),
        get_string('showdashboardwelcome_desc', 'theme_nadine'),
        1
    ));

    $page->add(new admin_setting_heading(
        'theme_nadine/dashboardwelcomeheading',
        get_string('dashboardwelcomeheading', 'theme_nadine'),
        get_string('dashboardwelcomeheading_desc', 'theme_nadine')
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/dashboardeyebrow',
        get_string('dashboardeyebrow', 'theme_nadine'),
        get_string('dashboardeyebrow_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtext(
        'theme_nadine/dashboardgreeting',
        get_string('dashboardgreeting', 'theme_nadine'),
        get_string('dashboardgreeting_desc', 'theme_nadine'),
        '',
        PARAM_TEXT
    ));

    $page->add(new admin_setting_configtextarea(
        'theme_nadine/dashboardintro',
        get_string('dashboardintro', 'theme_nadine'),
        get_string('dashboardintro_desc', 'theme_nadine'),
        '',
        PARAM_CLEANHTML,
        60,
        3
    ));

    $settings->add($page);

    // Appearance.
    $page = new admin_settingpage('theme_nadine_appearance', get_string('appearancesettings', 'theme_nadine'));

    $setting = new admin_setting_configcheckbox(
        'theme_nadine/enabledarkmode',
        get_string('enabledarkmode', 'theme_nadine'),
        get_string('enabledarkmode_desc', 'theme_nadine'),
        1
    );
    $setting->set_updatedcallback('theme_nadine_sync_colour_modes');
    $page->add($setting);

    $setting = new admin_setting_configselect(
        'theme_nadine/defaultcolourmode',
        get_string('defaultcolourmode', 'theme_nadine'),
        get_string('defaultcolourmode_desc', 'theme_nadine'),
        'auto',
        [
            'light' => get_string('colourmode:light', 'theme_boost'),
            'dark' => get_string('colourmode:dark', 'theme_boost'),
            'auto' => get_string('colourmode:auto', 'theme_boost'),
        ]
    );
    $setting->set_updatedcallback('theme_nadine_sync_colour_modes');
    $page->add($setting);

    $settings->add($page);

    // Advanced.
    $page = new admin_settingpage('theme_nadine_advanced', get_string('advancedsettings', 'theme_nadine'));

    $setting = new admin_setting_scsscode(
        'theme_nadine/customscss',
        get_string('customscss', 'theme_nadine'),
        get_string('customscss_desc', 'theme_nadine'),
        '',
        PARAM_RAW
    );
    $setting->set_updatedcallback('theme_reset_all_caches');
    $page->add($setting);

    $settings->add($page);
}
