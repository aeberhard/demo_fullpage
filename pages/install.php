<?php

$csrfToken = rex_csrf_token::factory('demo_fullpage');

/** @var rex_addon $this */

/* setup process */

if (rex_post('install', 'boolean') && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
} elseif (rex_post('install', 'boolean')) {

    $errors = array();

    // step 1/5: select missing packages we need to download
    $missingPackages = array();
    $packages = array();
    if (isset($this->getProperty('setup')['packages'])) {
        $packages = $this->getProperty('setup')['packages'];
    }
    if (count($packages) > 0) {

        // fetch list of available packages from to redaxo webservice
        try {
            $packagesFromInstaller = rex_install_packages::getAddPackages();
        } catch (rex_functional_exception $e) {
            $errors[] = $e->getMessage();
            rex_logger::logException($e);
        }

        if (count($errors) == 0) {
            foreach ($packages as $id => $fileId) {

                $localPackage = rex_package::get($id);
                if ($localPackage->isSystemPackage()) {
                    continue; // skip system packages, they don’t need to be downloaded
                }

                $installerPackage = isset($packagesFromInstaller[$id]['files'][$fileId]) ? $packagesFromInstaller[$id]['files'][$fileId] : false;
                if (!$installerPackage) {
                    $errors[] = $this->i18n('package_not_available', $id);
                }

                if ($localPackage->getVersion() !== $installerPackage['version']) {
                    $missingPackages[$id] = $fileId; // add to download list if package is not yet installed
                }
            }
        }
    }

    // step 2/5: download required packages
    if (count($missingPackages) > 0 && count($errors) == 0) {
        foreach ($missingPackages as $id => $fileId) {

            $installerPackage = $packagesFromInstaller[$id]['files'][$fileId];
            if ($installerPackage) {

                // fetch package
                try {
                    $archivefile = rex_install_webservice::getArchive($installerPackage['path']);
                } catch (rex_functional_exception $e) {
                    rex_logger::logException($e);
                    $errors[] = $this->i18n('package_failed_to_download', $id);
                    break;
                }

                // validate checksum
                if ($installerPackage['checksum'] != md5_file($archivefile)) {
                    $errors[] = $this->i18n('package_failed_to_validate', $id);
                    break;
                }

                // extract package (overrides local package if existent)
                if (!rex_install_archive::extract($archivefile, rex_path::addon($id), $id)) {
                    rex_dir::delete(rex_path::addon($id));
                    $errors[] = $this->i18n('package_failed_to_extract', $id);
                    break;
                }

                rex_package_manager::synchronizeWithFileSystem();
            }
        }
    }

    // step 3/5: install and activate packages based on install sequence from config
    if (count($this->getProperty('setup')['installSequence']) > 0 && count($errors) == 0) {
        foreach ($this->getProperty('setup')['installSequence'] as $id) {

            $package = rex_package::get($id);
            if ($package instanceof rex_null_package) {
                $errors[] = $this->i18n('package_not_exists', $id);
                break;
            }

            $manager = rex_package_manager::factory($package);

            try {
                $manager->install();
            } catch (rex_functional_exception $e) {
                rex_logger::logException($e);
                $errors[] = $this->i18n('package_failed_to_install', $id);
                break;
            }

            try {
                $manager->activate();
            } catch (rex_functional_exception $e) {
                rex_logger::logException($e);
                $errors[] = $this->i18n('package_failed_to_activate', $id);
                break;
            }
        }
    }

    // step 4/5: import database
    if (count($this->getProperty('setup')['dbimport']) > 0 && count($errors) == 0) {
        foreach ($this->getProperty('setup')['dbimport'] as $import) {
            $file = rex_backup::getDir() . '/' . $import;
            $success = rex_backup::importDb($file);
            if (!$success['state']) {
                $errors[] = $this->i18n('package_failed_to_import', $import);
            }
        }
   }

    // step 5/5: import files
    if (count($this->getProperty('setup')['fileimport']) > 0 && count($errors) == 0) {
        foreach ($this->getProperty('setup')['fileimport'] as $import) {
            $file = rex_backup::getDir() . '/' . $import;
            $success = rex_backup::importFiles($file);
            if (!$success['state']) {
                $errors[] = $this->i18n('package_failed_to_import', $import);
            }
        }
    }

    // show result messages
    if (count($errors) > 0) {
        echo rex_view::error("<p>" . $this->i18n('installation_error') . "</p><ul><li>" . implode("</li><li>", $errors) . "</li></ul>");
    } else {
        echo rex_view::success("<p>" . $this->i18n('installation_success') . "</p>");
    }
}

// --- NO_DIST

/* create distribution release */
$package = rex_package::get($this->getName());
$name = $package->getPackageId();
$version = $package->getVersion();

$content = '<p>Addon-Release für die Veröffentlichung erstellen: <strong>'.$name.'_'.$version.'.zip</strong></p>';
$content .= '<p>';
$content .= '<button class="btn btn-default" type="submit" name="release" value="1"><i class="rex-icon fa-gear"></i> Release erstellen</button>&nbsp;&nbsp;&nbsp;';
$content .= '<button class="btn btn-default" type="submit" name="git" value="1"><i class="rex-icon fa-gear"></i> GIT Release erstellen</button>';
$content .= '</p>';
$fragment = new rex_fragment();
$fragment->setVar('class', 'warning', false);
$fragment->setVar('title', 'Release erstellen', false);
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');
$content = '
<form action="' . rex_url::currentBackendPage() . '" method="post" data-confirm="Jetzt das Release zum veröffentlichen erstellen?">
    ' . $csrfToken->getHiddenField() . '
    ' . $content . '
</form>';

// create release
if (rex_post('release', 'boolean') && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
} elseif (rex_post('release', 'boolean')) {
    include(rex_path::addon($this->getName()).'release/release.php');
}
// create git release
if (rex_post('git', 'boolean') && !$csrfToken->isValid()) {
    echo rex_view::error(rex_i18n::msg('csrf_token_invalid'));
} elseif (rex_post('git', 'boolean')) {
    include(rex_path::addon($this->getName()).'release/git.php');
}

echo $content;

// --- /NO_DIST

/* setup info */

$content = '<p>' . $this->i18n('install_description') . '</p>';
$content .= '<p><button class="btn btn-send" type="submit" name="install" value="1"><i class="rex-icon fa-gear"></i> ' . $this->i18n('install_button') . '</button></p>';

$fragment = new rex_fragment();
$fragment->setVar('class', 'info', false);
$fragment->setVar('title', $this->i18n('install_heading'), false);
$fragment->setVar('body', $content, false);
$content = $fragment->parse('core/page/section.php');

$content = '
<form action="' . rex_url::currentBackendPage() . '" method="post" data-confirm="' . $this->i18n('confirm_setup') . '">
    ' . $csrfToken->getHiddenField() . '
    ' . $content . '
</form>';

echo $content;


/* package info from README.md */

$content = '';

$package = rex_package::get($this->getName());
$name = $package->getPackageId();
$version = $package->getVersion();
$author = $package->getAuthor();
$supportPage = $package->getSupportPage();

if (is_readable($package->getPath('README.md'))) {
    $fragment = new rex_fragment();
    $fragment->setVar('content', rex_markdown::factory()->parse(rex_file::get($package->getPath('README.md'))), false);
    $content .= $fragment->parse('core/page/docs.php');
}

if (!empty($content)) {
    $fragment = new rex_fragment();
    $fragment->setVar('title', rex_i18n::msg('package_help') . ' ' . $name, false);
    $fragment->setVar('body', $content, false);
    echo $fragment->parse('core/page/section.php');
}


/* credits */

$credits = '';
$credits .= '<dl class="dl-horizontal">';
$credits .= '<dt>' . rex_i18n::msg('credits_name') . '</dt><dd>' . htmlspecialchars($name) . '</dd>';

if ($version) {
    $credits .= '<dt>' . rex_i18n::msg('credits_version') . '</dt><dd>' . $version . '</dd>';
}
if ($author) {
    $credits .= '<dt>' . rex_i18n::msg('credits_author') . '</dt><dd>' . htmlspecialchars($author) . '</dd>';
}
if ($supportPage) {
    $credits .= '<dt>' . rex_i18n::msg('credits_supportpage') . '</dt><dd><a href="' . $supportPage . '" onclick="window.open(this.href); return false;">' . $supportPage . '</a></dd>';
}

$credits .= '</dl>';

$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('credits'), false);
$fragment->setVar('body', $credits, false);
echo $fragment->parse('core/page/section.php');
